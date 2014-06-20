<?php
/**
 * @param $origD - original downloads
 * @param $origI - original installations
 * @param $expD - experimental downloads
 * @param $expI - experimental installations
 * @return bool|float
 * return (bool)false if uncomputable, pvalue instead
 */
function pValue($origD, $origI, $expD, $expI) {

    function cumnormdist($x)
    {
        $b1 =  0.319381530;
        $b2 = -0.356563782;
        $b3 =  1.781477937;
        $b4 = -1.821255978;
        $b5 =  1.330274429;
        $p  =  0.2316419;
        $c  =  0.39894228;

        if($x >= 0.0) {
            $t = 1.0 / ( 1.0 + $p * $x );
            return (1.0 - $c * exp( -$x * $x / 2.0 ) * $t *
                ( $t *( $t * ( $t * ( $t * $b5 + $b4 ) + $b3 ) + $b2 ) + $b1 ));
        }
        else {
            $t = 1.0 / ( 1.0 - $p * $x );
            return ( $c * exp( -$x * $x / 2.0 ) * $t *
                ( $t *( $t * ( $t * ( $t * $b5 + $b4 ) + $b3 ) + $b2 ) + $b1 ));
        }
    }

    function seValue($c, $d) {
        $se = $c*(1-$c)/$d;
        return ($se >= 0 )?sqrt($se):false;
    }

    if($origD * $origI * $expD * $expI) {
        $origC = $origI/$origD;
        $expC = $expI/$expD;

        $origSE = seValue($origC, $origD);
        $expSE = seValue($expC, $expD);

        if($origSE === false || $expSE === false) return false;


        $z = ( $origC - $expC ) / sqrt( $origSE*$origSE + $expSE*$expSE );

        return cumnormdist($z);

    }
    else return false;
}

function yates($e, $s) {
    if ($e+ 0.5 < $s)
        $s -= 0.5;
    elseif ($e - 0.5 > $s)
        $s += 0.5;
    else
        $s = $e;

    return $s;
};

function gTest($origD, $origI, $expD, $expI) {



    if($origD * $origI * $expD * $expI) {
        $origD = $origD - $origI;
        $expD = $expD - $expI;

        $total = $origD + $origI + $expD + $expI;
        if(!$total) return false;

        $totalR1 = $origD + $origI;
        $totalR2 = $expD + $expI;

        $totalC1 = $origD + $expD;
        $totalC2 = $origI + $expI;

        $origDE = $totalR1 * $totalC1 / $total;
        $origIE = $totalR1 * $totalC2 / $total;
        $expDE = $totalR2 * $totalC1 / $total;
        $expIE = $totalR2 * $totalC2 / $total;

        $origD = yates($origDE, $origD);
        $origI = yates($origIE, $origI);
        $expD = yates($expDE, $expD);
        $expI = yates($expIE, $expI);

        if(!($origDE * $origIE * $expDE * $expIE)) return false;

        $result = 2*($origD*log($origD/$origDE) + $origI*log($origI/$origIE) + $expD*log($expD/$expDE) + $expI*log($expI/$expIE));

        return $result;
    }
    else return false;
}

function getChiSquare($x, $n) {
    if ( ($n==1) && ($x > 1000) ) {
        return 0;
    }

    if ( ($x>1000) || ($n>1000) ) {
        $q = getChiSquare(($x-$n)*($x-$n)/(2*$n),1) / 2;
        if($x > $n) {
            return $q;
        } else {
            return 1 - $q;
        }
    }
    $p = exp(-0.5 * $x);
    if(($n % 2) == 1) {
        $p = $p * sqrt(2*$x/pi());
    }
    $k = $n;
    while($k >= 2) {
        $p = $p * ($x/$k);
        $k = $k - 2;
    }
    $t = $p;
    $a = $n;
    while($t > 0.0000000001 * $p) {
        $a = $a + 2;
        $t = $t * ($x / $a);
        $p = $p + $t;
    }

    $retval = 1-$p;
    return $retval;
}

function confidence($origD, $origI, $hits, $convs) {
    $g = gTest($origD, $origI, $hits, $convs);
    if($g === false) return false;
    return round(100*(1-getChiSquare($g, 1)), 2);
}

function ba1($a, $b) {
    if($a*$b) {
        return round(100*($b/$a-1), 2);
    }

    return false;
}