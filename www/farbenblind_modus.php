<?php
    if (!isset($_SESSION["farbenblind_modus"])) {
        $_SESSION["farbenblind_modus"] = false;
    }

    function farbModus() {
            if ($_SESSION["farbenblind_modus"] === true) {
                $modeClass = "farbenblind";
            } else {
                $modeClass = "normal";
            }
            return $modeClass;
    }

    function farbwechsel(){
        $_SESSION["farbenblind_modus"] = !$_SESSION["farbenblind_modus"];
    }
?>