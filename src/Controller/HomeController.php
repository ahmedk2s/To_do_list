<?php
namespace App\Controller;



class HomeController {

    public function bonjour()
    {

        return new Response("bonjour à toutes et à tous");

    }
}