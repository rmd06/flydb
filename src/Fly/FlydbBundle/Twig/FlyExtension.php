<?php

namespace Fly\FlydbBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;

class FlyExtension extends Twig_Extension
{
    private $environment;
    
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
    
    public function getFilters()
    {
        return array(
            'daysToNow' => new Twig_Filter_Method($this, 'daysToNowFilter'),
        );
    }

    public function daysToNowFilter($date)
    {
        $env = $this->environment;
        
        $dated = twig_date_converter($env,$date);
    
        $interval = $dated->diff(new \DateTime("now"));
        
        if ('0' === $interval->format('%a') )
        {
            $outstring = $interval->format('today');
        }
        elseif ( '+' === $interval->format('%R') )
        {
            if ( '1' === $interval->format('%a') )
            {
                $outstring = $interval->format('%a day ago');
            } else 
            {
                $outstring = $interval->format('%a days ago');
            }
        }
        elseif ( '-' === $interval->format('%R') )
        {
            if ( '1' === $interval->format('%a') )
            {
                $outstring = $interval->format('%a day later');
            } else 
            {
                $outstring = $interval->format('%a days later');
            }
        }
        
        return $outstring;

    }

    public function getName()
    {
        return 'fly_extension';
    }
}
