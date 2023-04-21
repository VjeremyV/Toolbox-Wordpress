<?php
namespace App\Classes;


class FormChecker {

    /**
     * vérifie qu'une extension est bien celle que l'on attend
     *
     * @param string $extension
     * @param array $expect
     * @return void
     */
    public function check_file_extension(string $extension, array $expect){
      foreach($expect as $valid_extensions){
        if($extension === $valid_extensions){
            return true;
        }
      }
      return false;
    }
}
