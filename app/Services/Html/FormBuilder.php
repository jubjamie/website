<?php
namespace App\Services\Html;

use Carbon\Carbon;

class FormBuilder extends \Collective\Html\FormBuilder
{
	/**
	 * Create a dropdown group for hour and minute.
	 * @param       $name
	 * @param null  $selected
	 * @param array $options
	 * @return string
	 */
	public function selectTime($name, $selected = null, array $options = [])
	{
		$hours = $minutes = [];
		foreach(range(0, 23) as $hour) {
			$hours[$hour] = sprintf('%02d', $hour);
		}
		foreach(range(0, 59) as $minute) {
			$minutes[$minute] = sprintf('%02d', $minute);
		}

		return sprintf("%s : %s",
			$this->select($name . '_hour', $hours, $this->getValueAttribute($name . '_hour', Carbon::now()->hour), $options),
			$this->select($name . '_minute', $minutes, $this->getValueAttribute($name . '_minute', Carbon::now()->minute), $options));
	}

	/**
	 * Create a dropdown group for day, month and year.
	 * @param       $name
	 * @param null  $selected
	 * @param array $options
	 * @return string
	 */
	public function selectDate($name, $selected = null, array $options = [])
	{
		$days = [];
		foreach(range(1, 31) as $day) {
			$days[$day] = sprintf('%02d', $day);
		}

		return sprintf("%s / %s / %s",
			$this->select($name . '_day', $days, $this->getValueAttribute($name . '_day', Carbon::now()->day), $options),
			$this->selectMonth($name . '_month', $this->getValueAttribute($name . '_month', Carbon::now()->month), $options),
			$this->selectYear($name . '_year', date('Y') - 1, date('Y') + 1, $this->getValueAttribute($name . '_year', Carbon::now()->year), $options));
	}

	/**
	 * Create a dropdown group for hour, minute, day, month and year.
	 * @param       $name
	 * @param null  $selected
	 * @param array $options
	 * @return string
	 */
	public function selectDateTime($name, $selected = null, array $options = [])
	{
		return sprintf("%s&nbsp;&nbsp;%s",
			$this->selectTime($name, $selected, $options),
			$this->selectDate($name, $selected, $options));
	}

	/**
	 * Override the default 'date' type to enable the date/time picker.
	 * @param string $name
	 * @param null   $value
	 * @param array  $options
	 * @return string
	 */
	public function date($name, $value = null, $options = [])
	{
		$options = array_merge([
			'data-input-type'  => 'datetimepicker',
			'data-date-format' => 'YYYY-MM-DD',
			'placeholder'      => @$options['data-date-format'] ?: 'YYYY-MM-DD',
		], $options);

		return $this->text($name, $value, $options);
	}

	/**
	 * Override the default 'time' type to enable the date/time picker.
	 * @param string $name
	 * @param null   $value
	 * @param array  $options
	 * @return string
	 */
	public function time($name, $value = null, $options = [])
	{
		$options = array_merge([
			'data-input-type'  => 'datetimepicker',
			'data-date-format' => 'HH:mm:ss',
			'placeholder'      => @$options['data-date-format'] ?: 'HH:mm:ss',
		], $options);

		return $this->text($name, $value, $options);
	}

	/**
	 * Override the default 'datetime' type to enable the date/time picker.
	 * @param string $name
	 * @param null   $value
	 * @param array  $options
	 * @return string
	 */
	function datetime($name, $value = null, $options = [])
	{
		$options = array_merge([
			'data-input-type'  => 'datetimepicker',
			'data-date-format' => 'YYYY-MM-DD HH:mm:ss',
			'placeholder'      => @$options['data-date-format'] ?: 'YYYY-MM-DD HH:mm:ss',
		], $options);

		return $this->text($name, $value, $options);
	}
}