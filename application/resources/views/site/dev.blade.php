<!DOCTYPE HTML>
@include('site.bootstrapp')
<html>
	<head>
		<title>Разработка для amoCRM</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href={{ asset("site/css/main.css") }} />
	</head>
	<body class="no-sidebar is-preload">
		<div id="page-wrapper">

			<!-- Header -->
				<section id="header" class="wrapper">

					<!-- Logo -->
						<div id="logo">
							<h1><a href={{ route('site.index') }}>Разработка</a></h1>
							<p>Разработка это оч хорошо и классно<br>
								несите денежки будет круто</p>

							<ul class="actions special">
								<li><a href="#" class="button-left">Связаться</a></li>
								<li><a href="#" class="button-right">Подробности</a></li>
							</ul>
						</div>

					@include('site.nav')

				</section>

			<!-- Main -->
				<div id="main" class="wrapper style2">
					<div class="title">No Sidebar</div>
					<div class="container">

						<!-- Content -->
							<div id="content">
								<article class="box post">
									<header class="style1">
										<h2>Lorem ipsum dolor sit amet magna egestas<br class="mobile-hide" />
										morbi bibendum sed malesuada</h2>
										<p>Tempus feugiat veroeros sed nullam dolore</p>
									</header>
									<a href="#" class="image featured">
										<img src="images/pic01.jpg" alt="" />
									</a>
									<p>Fringilla nisl. Donec accumsan interdum nisi, quis tincidunt felis sagittis eget.
									odio eleifend. Duis commodo fringilla commodo. Aliquam erat volutpat. Vestibulum
									facilisis leo magna. Cras sit amet urna eros, id egestas urna. Quisque aliquam
									tempus euismod. Vestibulum ante ipsum primis in faucibus.</p>
									<p>Phasellus nisl nisl, varius id porttitor sed, pellentesque ac orci. Pellentesque
									habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi
									bibendum justo sed mauris vehicula malesuada aliquam elit imperdiet. Aliquam eu nibh
									lorem, eget gravida mi. Duis odio diam, luctus et vulputate vitae, vehicula ac dolor.
									Pellentesque at urna eget tellus lobortis ultrices sed non erat. Donec eget erat non
									magna volutpat malesuada quis eget eros. Nullam sodales cursus sapien, id consequat
									leo suscipit ut. Praesent id turpis vitae turpis pretium ultricies. Vestibulum sit
									amet risus elit.</p>
									<p>Donec consectetur vestibulum dolor et pulvinar. Etiam vel felis enim, at viverra
									ligula. Ut porttitor sagittis lorem, quis eleifend nisi ornare vel. Praesent nec orci
									eget quam rutrum bibendum. Proin pellentesque diam non ligula commodo tempor. Vivamus
									eget urna nibh. Curabitur non fringilla nisl. Donec accumsan interdum nisi, quis
									tincidunt felis sagittis eget. Donec elementum ligula dignissim sem pulvinar non semper
									odio eleifend. Duis commodo fringilla commodo. Aliquam erat volutpat. Vestibulum
									facilisis leo magna. Cras sit amet urna eros, id egestas urna. Quisque aliquam
									tempus euismod. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices
									posuere cubilia.</p>
									<p>Phasellus nisl nisl, varius id porttitor sed, pellentesque ac orci. Pellentesque
									habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi
									bibendum justo sed mauris vehicula malesuada aliquam elit imperdiet. Aliquam eu nibh
									lorem, eget gravida mi. Duis odio diam, luctus et vulputate vitae, vehicula ac dolor.
									Pellentesque at urna eget tellus lobortis ultrices sed non erat. Donec eget erat non
									magna volutpat malesuada quis eget eros. Nullam sodales cursus sapien, id consequat
									leo suscipit ut. Praesent id turpis vitae turpis pretium ultricies. Vestibulum sit
									amet risus elit.</p>
								</article>
							</div>

					</div>
				</div>

			<!-- Highlights -->
				<section id="highlights" class="wrapper style3">
					<div class="title">The Endorsements</div>
					<div class="container">
						<div class="row aln-center">
							<div class="col-4 col-12-medium">
								<section class="highlight">
									<a href="#" class="image featured"><img src="images/pic02.jpg" alt="" /></a>
									<h3><a href="#">Aliquam diam consequat</a></h3>
									<p>Eget mattis at, laoreet vel amet sed velit aliquam diam ante, dolor aliquet sit amet vulputate mattis amet laoreet lorem.</p>
									<ul class="actions">
										<li><a href="#" class="button style1">Learn More</a></li>
									</ul>
								</section>
							</div>
							<div class="col-4 col-12-medium">
								<section class="highlight">
									<a href="#" class="image featured"><img src="images/pic03.jpg" alt="" /></a>
									<h3><a href="#">Nisl adipiscing sed lorem</a></h3>
									<p>Eget mattis at, laoreet vel amet sed velit aliquam diam ante, dolor aliquet sit amet vulputate mattis amet laoreet lorem.</p>
									<ul class="actions">
										<li><a href="#" class="button style1">Learn More</a></li>
									</ul>
								</section>
							</div>
							<div class="col-4 col-12-medium">
								<section class="highlight">
									<a href="#" class="image featured"><img src="images/pic04.jpg" alt="" /></a>
									<h3><a href="#">Mattis tempus lorem</a></h3>
									<p>Eget mattis at, laoreet vel amet sed velit aliquam diam ante, dolor aliquet sit amet vulputate mattis amet laoreet lorem.</p>
									<ul class="actions">
										<li><a href="#" class="button style1">Learn More</a></li>
									</ul>
								</section>
							</div>
						</div>
					</div>
				</section>

			<!-- Footer -->
			@include('site.footer')

		</div>

	</body>
</html>