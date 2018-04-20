<?php

namespace UIFactory\Component;

use Exception;
use UIFactory\Helper\ComponentDirector;
use UIFactory\Helper\ComponentProperty;

abstract class Base
{
	use ComponentDirector;
	use ComponentProperty;

	/**
	 * @var array HTML attributes
	 */
	protected $attributes = [
		'style' => [],
		'class' => ''
	];

	protected $props = [];

	protected $requiredProps = [];

	protected $propTypes = [];

	private $availablePropTypeRules = [
		'type', 'in', 'not_in'
	];

	protected $configs = [
		'PROP_VALIDATION' => false
	];
	
	/**
	 * @var string Base's inner HTML
	 */
	protected $content = '';

	/**
	 * HTML markup of this component
	 *
	 * @return string HTML markup
	 */
	abstract protected function markup() : string;

	/**
	 * Set theme and echo this component if requires
	 *
	 * @uses Base::print() to echo component
	 *
	 * @param mixed $echo Echo the component immediately?
	 * @return void
	 */
	public function __construct(array $props = [], $echo = 1)
	{
		$this->prop($props);

		if ($echo) {
			$this->print($echo);
		}
	}

	public function get($amount = 1)
	{
		return $this->getHTML($amount);
	}

	/**
	 * Echo markup
	 *
	 * @uses Base::markup() to get component HTML markup
	 *
	 * @return void
	 */
	public function print($amount = 1)
	{
		echo $this->getHTML($amount);
	}

	public function getHTML($amount = 1)
	{
		if (! isset($this->html) || empty($this->html)) {
			$this->make($amount);
		}

		return $this->html;
	}

	/**
	 * Return component markup
	 *
	 * @uses Base::markup() to get component HTML markup
	 *
	 * @return string HTML markup
	 */
	public function make($amount = 1)
	{
		$this->checkRequiredProps();

		if ($amount > 1) {
			$this->makeMultiple($amount);
		} elseif ($amount === 1) {
			$this->html = $this->markup();
		}

		return $this;
	}

	protected function makeMultiple(int $amount)
	{
		$markups = '';

		foreach (range(1, $amount) as $index) {
			$markups .= $this->markup();
		}

		$this->html = $markups;
	}

	/**
	 * Set component's inner HTML
	 *
	 * @param string $content Content to set
	 * @return Base
	 */
	public function content(string $content)
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * Append content to component's inner HTML by string concatenation
	 *
	 * @param string $content Content to append
	 * @return Base
	 */
	public function appendContent(string $content)
	{
		$this->content = $this->content . $content;
		return $this;
	}

	/**
	 * Prepend component's inner HTML by string concatenation
	 *
	 * @param string $content Content to prepend
	 * @return Base
	 */
	public function prependContent(string $content)
	{
		$this->content = $content . $this->content;
		return $this;
	}

	/**
	 * Base configurations
	 *
	 * @param
	 * @param
	 * @return
	 */
	public function config(string $name, $value = null)
	{
		if (! isset($this->configs[$name])) {
			return null;
		}

		if (is_null($value)) {
			return $this->configs[$name];
		}

		$this->configs[$name] = $value;
		return $this;
	}

	public function condition(callable $callback)
	{
		call_user_func_array($callback, [$this]);
		return $this;
	}
}