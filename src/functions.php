<?php
if ( ! function_exists('duck')) {
    function duck_check($object): \AlexeyYashin\Ducktyping\Duck
    {
        return new \AlexeyYashin\Ducktyping\Duck($object);
    }
}
