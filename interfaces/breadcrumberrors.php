<?php

namespace WoocommerceExtra\Interfaces;

//Error constants for Breadcrumb class
interface BreadcrumbErrors{
    //Numbers
    const NOCATEGORIES = 1; //Categories list array is void

    //Messages
    const NOCATEGORIES_MSG = "La lista delle categorie è vuota0";

    //Other
    //Error of superclass Breadcrumb are between ERROR_MIN and ERROR_MAX
    const ERROR_MIN = 1;  
    const ERROR_MAX = 20;
}
?>