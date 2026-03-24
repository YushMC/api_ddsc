<?php

function getBandera($id){
    $array = array("",
    "assets/banderas/argentina.jpg",
    "assets/banderas/bolivia.png",
    "assets/banderas/chile.jpg",
    "assets/banderas/colombia.jpg",
    "assets/banderas/Costa_Rica.png",
    "assets/banderas/cuba.jpg",
    "assets/banderas/ecuador.jpg",
    "assets/banderas/el_salvador.png",
    "assets/banderas/espana.jpg",
    "assets/banderas/eua.jpg",
    "assets/banderas/guatemala.png",
    "assets/banderas/Honduras.png",
    "assets/banderas/mexico.jpg",
    "assets/banderas/nicaragua.jpg",
    "assets/banderas/panama.jpg",
    "assets/banderas/Paraguay.png",
    "assets/banderas/peru.png",
    "assets/banderas/Republica_Dominicana.png",
    "assets/banderas/uruguay.png",
    "assets/banderas/venezuela.png");
    if($id == 0){
        return "";
    }else{
        return "https://antiguo.dokidokispanish.club/".$array[$id];
    }
}