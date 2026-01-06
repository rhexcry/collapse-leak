<?php

declare(strict_types=1);

namespace collapse\feature;

final class FeatureManager{

	/** @var array<class-string, IFeature> */
	private array $features = [];

	public function __construct(private FeatureContext $context){
	}

	public function register(string $featureClass) : void{
		$feature = new $featureClass();
		$feature->initialize($this->context);

		if($feature instanceof TriggerableFeature){
			if(($triggerManager = $this->context->getTriggerManager()) === null){
				throw new \RuntimeException("Failed to register triggerable feature: TriggerManager is not initialized in FeatureContext");
			}
			$triggerManager->registerMultiple(
				$feature->getTriggers()
			);
		}

		$this->features[$featureClass] = $feature;
		$this->context->getEventBus()->subscribeFeature($feature);
	}

	/**
	 * @template T of IFeature
	 * @param class-string<T> $featureClass
	 * @return T
	 */
	public function get(string $featureClass) : IFeature{
		return $this->features[$featureClass] ?? throw new \RuntimeException("Feature $featureClass not registered");
	}

	public function getContext() : FeatureContext{
		return $this->context;
	}

	public function close() : void{
		foreach($this->features as $feature){
			$feature->shutdown();
		}
	}
}
