<?php 
/**
 * Convert Shell
 */

class ConvertShell extends Shell {


	function main(){
		 App::import('Component', 'Mediaserver');
         $this->Mediaserver = new MediaserverComponent();
		 
		 App::import('Controller', 'Context');
		 $this->Context = new ContextController();
		 $this->Context->constructClasses();
		 $this->out( $this->Mediaserver->convertMedia($this->Context, array()) );
		 exit(0);
	}
}

