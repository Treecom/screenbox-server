<header class="jumbotron subhead" id="overview">
  <h1>Welcome on Screenbox Server</h1>
  <p class="lead">Please login.</p>
</header>


<div class="login">
<?php

    echo $form->create("User",array('url'=>'/login'));
    echo $form->input("User.email");
    echo $form->input("User.password");
    echo $form->button('<span class="icon-save"></span> '. __('Login', true), array('class' => 'btn btn-primary'));
    echo $form->end(null);

?>
</div>