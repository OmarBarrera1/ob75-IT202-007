<?php
//UCID - ob75  - 11/13/2024
function reset_session()
{
    session_unset();
    session_destroy();
    session_start();
}