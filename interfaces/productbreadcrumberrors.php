<?php

namespace WoocommerceExtra\Interfaces;

//Error constants of ProductBreadcrumb class
interface ProductBreadcrumbErrors{
    //Exceptions
    const INCORRECTPATTERN_EXC = "La stringa passata che contiene le categorie del prodotto, ha un formato sconosciuto";
    const URL_CAT_ARRAY_LENGTHMISMATCH_EXC = "La lunghezza degli array dei nomi delle cateogie e degli indirizzi non è identica";
    const NO_WC_PRODUCT_INSTANCE_EXC = "L'oggetto WC_Product fornito è uguale a null";
    const INVALIDPRODUCTTYPE_EXC = "Il prodotto passato non è un oggetto WC_Product";

    //Other
    //Error of ProductBreadcrumb class are between ERROR_MIN and ERROR_MAX
    const PBE_ERROR_MIN = 21;  
    const PBE_ERROR_MAX = 40;
}


?>