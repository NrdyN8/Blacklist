<?php namespace NrdyN8\Blacklist;

use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

class BlacklistServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(){
        Validator::extend('blacklist', function($attribute, $value, $parameters, $validator){
            $parameters = array_map('trim', $parameters);
            $fileMimeType = explode('/', $value->getMimeType());
            $fileExtension = explode('.', $value->getClientOriginalName());
            if(in_array($fileMimeType[0],$parameters)) return false;
            if(in_array($fileMimeType[1], $parameters)) return false;
            if(in_array(end($fileExtension), $parameters))return false;
            return true;
        });
    }
}

