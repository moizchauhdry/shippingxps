<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Package;
use App\Models\Shipping;
use App\Models\User;
use App\Models\GiftCard;
use App\Models\GiftCardComment;
use App\Models\GiftCardFile;
use Inertia\Inertia;
use Auth;
use App\Notifications\GiftCardNotification;
use Carbon\Carbon;
use File;
use Illuminate\Validation\Rule;


class GiftCardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->type == 'admin') {
            $gift_cards = GiftCard::orderBy('id','desc')->get();
        } else {
            $gift_cards = GiftCard::where('user_id', $user->id)->get();
        }
        
        return Inertia::render('GiftCard/Index',[
            'gift_cards' => $gift_cards,
        ]);

    }

    public function create(Request $request)
    {
        $user = Auth::user();

        if ($request->isMethod('POST')) {
                
            $validated_data = $request->validate([
                'title' => 'required|string|max:150',
                'type' => 'required|string|in:PHYSICAL,ELECTRONIC',
                'amount' => 'required|numeric',
                'qty' => 'required|integer',
                'notes' => 'required|max:1500',
            ]);

            $gift_card = GiftCard::create([
                'user_id' => $user->id,
                'title' => $validated_data['title'],
                'type' => $validated_data['type'],
                'amount' => $validated_data['amount'],
                'qty' => $validated_data['qty'],
                'notes' => $validated_data['notes'],
            ]);

            $url = \URL::route('gift-card.edit', $gift_card->id);
            $data = [
                'url' => \URL::route('gift-card.edit', $gift_card->id),
                'message' => 'Customer has created an gift card request. <a href="' . $url . '">Click Here</a>',
            ];
            $admins = User::where('type', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new GiftCardNotification($data));
            }

            return redirect()->route('gift-card.index')->with('success', 'Successfully Requested');
        }

        return Inertia::render('GiftCard/Create');
    }

    public function edit(Request $request,$id)
    {
        $user = Auth::user();
        $gift_card = GiftCard::with('files')->findOrFail($id);
        
        if ($request->isMethod('POST')) {

            $validated_data = $request->validate([
                'title' => 'required|string|max:150',
                'type' => 'required|string|in:PHYSICAL,ELECTRONIC',
                'amount' => 'required|numeric',
                'qty' => 'required|integer|between:1,10',
                'notes' => 'required|max:1500',
                'status' => [Rule::requiredIf($user->type == 'admin')],
            ]);

            $gift_card->update([
                'title' => $request->title,
                'type' => $request->type,
                'amount' => $request->amount,
                'qty' => $request->qty,
                'notes' => $request->notes,
                'status' => $request->status,
            ]);

            $files = $request->file();
            if (isset($files['images'])) {
                foreach ($files['images'] as $key => $file) {
                    $image_object = $file['image'];
                    $file_name = time() . '_' . $image_object->getClientOriginalName();
                    $image_object->storeAs('uploads', $file_name);
                    if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('/public/uploads/' . $file_name));
                    } else {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('../public/uploads/' . $file_name));
                    }

                    $gift_card_file = new GiftCardFile();
                    $gift_card_file->file_name = $file_name;
                    $gift_card_file->gift_card_id = $gift_card->id;
                    
                    if ($key == 0) {
                        $gift_card_file->display = 1;
                    } else {
                        $gift_card_file->display = 0;
                    }
                    
                    $gift_card_file->save();
                }
            }

            $url = \URL::route('gift-card.edit', $gift_card->id);
            $auser = $user->type == 'admin' ? 'Admin' : 'User';
            $data = [
                'url' => \URL::route('gift-card.edit', $gift_card->id),
                'message' => $auser . ' has modified an gift card request. <a href="' . $url . '">Click Here</a>',
            ];

            if ($user->type == 'customer') {
                $customer = User::find($gift_card->user_id);
                $customer->notify(new GiftCardNotification($data));
            } else {
                $admins = User::where('type', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new GiftCardNotification($data));
                }
                $changes = $gift_card->getChanges();
                if (isset($changes) && $changes != NULL) {
                    $gift_card->update(['admin_updated_at' => Carbon::now()]);
                    $gift_card->status == 'Accepted' ? $gift_card->update(['admin_approved_at' => Carbon::now()]) : $gift_card->update(['admin_approved_at' => NULL]);
                }
            }
            
            if ($request->has('approve') && $request->approve == 1) {
                \Session::forget(['order_id','package_id','gift_card_id']);
                \Session::put('insurance_id', $id);
                
                $amount = $gift_card->amount * $gift_card->qty;

                if ($amount <= 0) {
                    return redirect()->back()->with('error', 'OPERRATION FAILED TO PERFORM, AMOUNT MUST BE GREATER THAN 0');
                }

                if ($amount > 5) {
                    $percentage_amount =  $amount * 5 / 100;
                    $final_amount = $amount + $percentage_amount;
                } else {
                    $final_amount =  $amount + 5;
                }

                if ($gift_card->type == 'PHYSICAL') {
                    $final_amount = $final_amount + 25;
                }

                return redirect()->route('payment.index', 'amount=' . $final_amount);
            }
            
            return redirect()->route('gift-card.index')->with('success', 'Successfully Modified');
        }

        $gift_card = GiftCard::find($id);
        $comments = GiftCardComment::where('gift_card_id',$gift_card->id)->with('user')->orderBy('id','desc')->get();

        $images = [];
        foreach ($gift_card->files as $image) {
            $images[] = [
                'id' => $image->id,
                'image' => $image->image,
            ];
        }
        
        return Inertia::render('GiftCard/Edit', [
            'gift_card' => $gift_card,
            'comments' => $comments,
            'images' => $images,
        ]);
    }

    public function storeComment(Request $request, $id)
    {
        $user = \Auth::user();
        $gift_card = GiftCard::find($id);
        $validated_data = $request->validate([
            'message' => 'required',
        ]);

        GiftCardComment::create([
            'gift_card_id' => $id,
            'user_id' => $user->id,
            'message' => $validated_data['message'],
        ]);

        $url = \URL::route('gift-card.edit', $gift_card->id);
        $auser = $user->type == 'admin' ? 'Admin' : 'User';
        $data = [
            'url' => \URL::route('gift-card.edit', $gift_card->id),
            'message' => $auser . ' has commented on an gift card request. <a href="' . $url . '">Click Here</a>',
        ];
        if ($user->type == 'admin') {
            $customer = User::find($gift_card->user_id);
            $customer->notify(new GiftCardNotification($data));
        } else {
            $admins = User::where('type', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new GiftCardNotification($data));
            }
        }
        $gift_card = GiftCard::find($id);
        $comments = GiftCardComment::where('gift_card_id',$gift_card->id)->with('user')->orderBy('id','desc')->get();

        return Inertia::render('GiftCard/Edit', [
            'gift_card' => $gift_card,
            'comments' => $comments,
        ]);
    }

    public function deleteImage(Request $request)
    {
        $image_id = $request->input('id');
        GiftCardFile::find($image_id)->delete();
    }
}
