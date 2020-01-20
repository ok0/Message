<?php
namespace Message;

use Message\Invoker\Invoker;
use Message\Invoker\InvokerInterface;
use Message\Resolver\ResolverInterface;

/**
 * 외부 메시지 전송 패키지
 * 
 * @author      github.com/ok0
 * @copyright   Copyright (c) 2019, github.com/ok0
 */
class Message
{
    /**
     * @var ResolverInterface
     */
    private $resolver = NULL;

    /**
     * @var InvokerInterface
     */
    private $invoker = NULL;

    /**
     * @return Report\ReportInterface[]
     */
    public function run() {
		$result = NULL;
		
		$invoker = $this->getInvoker();
		$resolver = $this->getResolver();
		
		$reports = $invoker->execute($resolver->resolve());
		
        return $reports;
    }
    
    /**
     * 
     * @return ResolverInterface
     * 
     */
    public function getResolver() {
        return $this->resolver;
    }

    /**
     * @return InvokerInterface
     */
    public function getInvoker() {
        if (is_null($this->invoker)) {
            $this->invoker = new Invoker();
        }
        
        return $this->invoker;
    }

    /**
     * @param ResolverInterface $resolver
     * 
     */
	public function setResolver(ResolverInterface $resolver) {
        $this->resolver = $resolver;
    }

    /**
     * @param InvokerInterface $invoker
     */
    public function setInvoker(InvokerInterface $invoker) {
        $this->invoker = $invoker;
    }
}