<head>
        <title>Cross Language Scripting</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<link rel="stylesheet" href="https://code.getmdl.io/1.1.3/material.indigo-pink.min.css">
		<script defer src="https://code.getmdl.io/1.1.3/material.min.js"></script>

		<script src="js/jquery.js"></script>

		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	</head>

    <body>

		<div class="demo-layout-waterfall mdl-layout mdl-js-layout">
		    <header class="mdl-layout__header mdl-layout__header--waterfall">
		    <!-- Top row, always visible -->
		    <div class="mdl-layout__header-row">
		      <!-- Title -->
		        <span class="mdl-layout-title">Cross Language Scripting</span>
		        <div class="mdl-layout-spacer"></div>
		        <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable
		                  mdl-textfield--floating-label mdl-textfield--align-right">
			        <label class="mdl-button mdl-js-button mdl-button--icon"
			               for="waterfall-exp">
			            <i class="material-icons">search</i>
		       		</label>
			        <div class="mdl-textfield__expandable-holder">
			          	<input class="mdl-textfield__input" type="text" name="sample"
			                 	id="waterfall-exp">
			        </div>
		      	</div>
		    </div>
		    <!-- Bottom row, not visible on scroll -->
		    <div class="mdl-layout__header-row">
		      <div class="mdl-layout-spacer"></div>
		      <!-- Navigation -->
		      <nav class="mdl-navigation">
		        <span class="mdl-navigation__link" href="">Welcome, <?php echo $_SESSION["username"]; ?></span>
		        <a class="mdl-navigation__link" href="upload.php">Upload</a>
			    <a class="mdl-navigation__link" href="">About Us</a>

		      </nav>
		    </div>
		  	</header>

			<main class="mdl-layout__content">
			    <div class="page-content">