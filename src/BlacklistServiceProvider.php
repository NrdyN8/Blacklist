<?php namespace NrdyN8\Blacklist;

use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

class BlacklistServiceProvider extends ServiceProvider
{

    public function isWhiteListed($mimeType, $parameters){
        if(in_array("!".$mimeType[1], $parameters)) return true;
        return false;
    }

    private function getTrueFileExtension($extension){
        if(count($extension) == 1){ //File doesn't have am extension
            return null;
        }
        elseif(count($extension) == 3){//Dual extensions is a common way to bypass security
            return $extension[1];
        }
        else{

            return end($extension);
        }
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(){
        Validator::extend('blacklist', function($attribute, $value, $parameters, $validator){
            $parameters = array_map('trim', $parameters);
            if(in_array("nospecialchars", $parameters)){
                $invalidChars = "/[^A-Za-z0-9\- .(){}\[\]]/";
                if(preg_match($invalidChars, $value->getClientOriginalName()))return false;
            }
            $fileMimeType = explode('/', $value->getMimeType());
            $fileExtension = explode('.', $value->getClientOriginalName());
            $fileExtension = $this->getTrueFileExtension($fileExtension);
            if(in_array($fileMimeType[0], $parameters) || in_array($fileMimeType[1], $parameters)) {
                return $this->isWhiteListed($fileMimeType, $parameters);
            }
            return true;
        });
    }
}

