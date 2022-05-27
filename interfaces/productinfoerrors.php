<?php

namespace WoocommerceExtra\Interfaces;

interface ProductInfoErrors{
    const FILENOTEXISTS_EXC = "Il file specificato non esiste";
    const INVALIDTYPE_EXC = "Il tipo di file specificato non è valido";
    const PATHNOTSPECIFIED_EXC = "Non è stato specificato un file da aprire";
    const UNEXPECTEDCONTENT_EXC = "Il contenuto del file JSON ha delle proprietà diverse da quelle aspettate";
}
?>