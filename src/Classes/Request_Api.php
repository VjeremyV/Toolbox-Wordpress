<?php 
namespace App\Classes;

class Request_Api {
 
    // Method: POST, PUT, GET etc
    // Data: array("param" => "value") ==> index.php?param=value
    
    Public function CallAPI($method, $url, $data = false)
    {
        // on initialise une session curl
    $curl = curl_init();
    
    // on utilise switch pour gérer les différentes methodes possibles 
    switch ($method)
    {
        case "POST":
            // on prépare les options pour le transfert
            curl_setopt($curl, CURLOPT_POST, 1);
            // si la data existe on l'intègre au transfere
            if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
            //on fait de même avec put
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
                default:
                if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
            }
            
            // Optional Authentication:
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, "username:password");
            
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            
            $result = curl_exec($curl);
            
            curl_close($curl);
            
            return $result;
        }
    }