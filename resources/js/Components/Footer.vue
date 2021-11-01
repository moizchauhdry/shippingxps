<template>

			<footer id="footer" class="border-top-0 m-0" style="background-size: cover; background-position: center; background-repeat: no-repeat;">
				<div class="container pt-3">
					<div class="row pt-3 mt-5">
						<div class="col-lg-3 mb-4 mb-lg-0">
							<a href="/" class="text-decoration-none">
								<img src="/theme/img/logo.png" class="img-fluid mb-4" width="123" height="33" alt="" />
							</a>
							<ul class="list list-icons list-icons-lg">
								<li class="d-flex px-0 mb-1">
									<!-- <img width="25" src="/theme/img/demos/business-consulting-3/icons/phone.svg" alt="Phone Icon" /> -->
									<a href="tel:13022650777" class="text-color-light font-weight-semibold text-1 ms-2"> +1 (302) 265 0 777</a>
								</li>
								<li class="d-flex px-0 my-3">
									<!-- <img width="25" src="/theme/img/demos/business-consulting-3/icons/email.svg" alt="Email Icon" /> -->
									<a href="mailto:shippingxps@shippingxpstheme.com" class="text-color-light font-weight-semibold text-1 ms-2"> @shippingxps.com</a>
								</li>
								<li class="d-flex font-weight-semibold text-color-light text-1 px-0 mb-1">
									<!-- <img width="25" src="/theme/img/demos/business-consulting-3/icons/map-pin.svg" alt="Location" /> -->
									3578 W SAVANNA ST , ANAHEIM , State: CA , 92804
								</li>
							</ul>
							<ul class="social-icons social-icons-clean social-icons-clean-with-border social-icons-medium social-icons-icon-light">
								<li class="social-icons-facebook"><a href="http://www.facebook.com/" target="_blank" title="Facebook" data-cursor-effect-hover="fit"><i class="fab fa-facebook-f"></i></a></li>
								<li class="social-icons-twitter mx-2"><a href="http://www.twitter.com/" target="_blank" title="Twitter" data-cursor-effect-hover="fit"><i class="fab fa-twitter"></i></a></li>
								<li class="social-icons-instagram"><a href="http://www.instagram.com/" target="_blank" title="instagram" data-cursor-effect-hover="fit"><i class="fab fa-instagram"></i></a></li>
								<li class="social-icons-linkedin"><a href="http://www.linkedin.com/" target="_blank" title="Linkedin" data-cursor-effect-hover="fit"><i class="fab fa-linkedin-in"></i></a></li>
								<li class="social-icons-youtube"><a href="http://www.youtube.com/" target="_blank" title="youtube" data-cursor-effect-hover="fit"><i class="fab fa-youtube"></i></a></li>
								<li class="social-icons-pinterest"><a href="http://www.pinterest.com/" target="_blank" title="pinterest" data-cursor-effect-hover="fit"><i class="fab fa-pinterest"></i></a></li>
							</ul>
						</div>
						<div class="col-lg-3">
							<h4 class="font-weight-bold text-3">COMPANY</h4>
							<ul class="list list-icons list-icons-sm">
								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<a href="/how-it-works" class="link-hover-style-1 ms-1 "> How it works</a>
								</li>
								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<inertia-link :href="route('shipping-calculator')" class="link-hover-style-1 ms-1">Shipping Calculator</inertia-link>
								</li>
								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<inertia-link :href="route('pricing')" class="link-hover-style-1 ms-1">Services</inertia-link>
								</li>
							</ul>
						</div>
						<div class="col-lg-3">
							<h4 class="font-weight-bold text-3">OUR SERVICES</h4>
							<ul class="list list-icons list-icons-sm">
								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<a href="/store-and-forward" class="link-hover-style-1 ms-1 "> Store & Forward</a>
								</li>
								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<a href="/consolidation" class="link-hover-style-1 ms-1 "> Consolidation</a>
								</li>

								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<a href="/online-shopping-assistant" class="link-hover-style-1 ms-1 "> Online Shopping Assistant</a>
								</li>
								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<a href="/package-returns" class="link-hover-style-1 ms-1 "> Package returns</a>
								</li>

							</ul>
						</div>
						<div class="col-lg-3">
							<h4 class="font-weight-bold text-3">SUPPORT</h4>
							<ul class="list list-icons list-icons-sm">
								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<a href="/customer-service" class="link-hover-style-1 ms-1 "> Customer Service</a>
								</li>
								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<a href="/service-updates" class="link-hover-style-1 ms-1 "> Service Updates</a>
								</li>
								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<a href="/unaccapted-items" class="link-hover-style-1 ms-1 "> Unaccapted Items</a>
								</li>
								<li>
									<i class="fas fa-angle-right text-color-default"></i>
									<a href="/policies-terms" class="link-hover-style-1 ms-1 "> Policies & Terms</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
	
			</footer>
</template>
<style scoped>
    #footer ul li a{ font-size: 14.5px; font-weight: 600;}
    .destinations h5{ font-size: 16px; font-weight: 500;}
    #footer .badge{ font-size: 14.5px;}
    .margin-right-20{ margin-right: 4px;}
</style>
<script>
export default {
        props: ['showTicker'],
        created() {
            console.log(this.showTicker);
        },
        mounted() {
          this.initTawkTo();
        },
        methods:{
          initTawkTo(){
            axios.get("/checkAuth-user")
                .then(({ data }) => {
                      if(data.init){
                        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
                        (function(){
                          var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                          s1.async=true;
                          s1.src='https://embed.tawk.to/617e40def7c0440a5920c3c5/1fjaiqq44';
                          s1.charset='UTF-8';
                          s1.setAttribute('crossorigin','*');
                          s0.parentNode.insertBefore(s1,s0);
                        })();
                      }
                    }
                );
          }
  }
    }
</script>
