<?php

use App\Models\Prize;

$current_probability = floatval(Prize::sum('probability'));
?>
{{-- TODO: add Message logic here --}}
