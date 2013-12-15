<?php 

/**
 * Color Library
 *
 * Converts between RGB and HSL color spaces, allowing the dynamic adjustment of any part of the
 * HSL with automagic conversion back to RGB (hex and dec values).
 *
 * RGB hex values may be 3 or 6 digits in length, and contain the # symbol or not.
 *
 * :TODO:
 *
 * Add difference calculations. @see http://www.splitbrain.org/blog/2008-09/18-calculating_color_contrast_with_php
 *
 * NOTE: This is a re-write and conversion to a usable PHP class of code from various sources around the
 * internet.
 *
 *
 * @author Chris Mospaw
 */
class Color_Library
{
	protected $_rgb_hex = NULL; // hex value of RGB string
	protected $_rgb_dec = array // Array of HSL float values
	(
		'red' => 0,
		'green' => 0,
		'blue' => 0,
	); // array of integers
	protected $_hsl = array // Array of HSL float values
	(
		'hue' => 0,
		'saturation' => 0,
		'luminance' => 0,
	);

	// Logical max values
	const RGB_MAX = 255;
	const HUE_MAX = 360;

	// Arbitrary max values
	const SATURATION_MAX = 255;
	const LUMINANCE_MAX = 255;

	// Internals to avoid bad magic numbers
	const TYPE_RGB_HEX = 1;
	const TYPE_RGB_DEC = 2;
	const TYPE_HSL = 3;
	const TYPE_HSL_INTERNAL = 4;


	/**
	 * Set up the object.
	 *
	 *  If a color is specified, it is set up.
	 *
	 * @param object $color [optional]
	 * @param object $type [optional]
	 * @return
	 */
	public function __construct($color = NULL, $type = self::TYPE_RGB_HEX)
	{
		if ($color !== NULL)
		{
			$this->set_color($color, $type);
		}
	}


	/**
	 * Set the working color.
	 *
	 * Default is RGB_HEX value, but others may be specified.
	 *
	 * @param object $color
	 * @param object $type [optional]
	 * @return
	 */
	public function set_color ($color, $type = self::TYPE_RGB_HEX)
	{
		switch ($type)
		{
			// Hex string RGB value AABBCC
			case self::TYPE_RGB_HEX:
				$this->_rgb_dec = $this->rgb_hex_to_dex($color);
				$this->_rgb_hex = $this->rgb_dec_to_hex($this->_rgb_dec);
				$this->make_hsl();
		 	break;

			// Decimal array of RGB values
			case self::TYPE_RGB_DEC:
				$this->_rgb_hex = $this->rgb_dec_to_hex($color);
				$this->_rgb_dec = $this->rgb_hex_to_dex($this->_rgb_hex);
				$this->make_hsl();
		 	break;

			// Integer ranges (0 - xxx_MAX) for HSL values
			case self::TYPE_HSL:
				$color['hue'] /= self::HUE_MAX;
				$color['saturation'] /= self::SATURATION_MAX;
				$color['luminance'] /= self::LUMINANCE_MAX;
				$this->_hsl = $color;
				$this->make_rgb();
			break;

			// Float ranges (0-1) for HSL values (for internal use)
			case self::TYPE_HSL_INTERNAL:
				$this->_hsl = $color;
				$this->make_rgb();
			break;

			// This should never execute, but you never know
			default:
				throw new Exception('Color Type is required');
		}
	}


	/**
	 * When printed, simply return the hex value, since it's what we usually want.
	 *
	 * @return current rgb_hex value
	 */
	public function __toString()
	{
		return (string) $this->_rgb_hex;
	}


	/**
	 * Setter getter via RGB space in HEX
	 *
	 * @param object $color [optional]
	 * @return
	 */
	public function rgb_hex($color = NULL)
	{
		if ($color !== NULL)
		{
			$this->set_color($color, self::TYPE_RGB_HEX);
		}
		return $this->_rgb_hex;
	}


	/**
	 * Setter getter via RGB space in array of decimal values
	 *
	 * @param object $color [optional]
	 * @return
	 */
	public function rgb_dec($color = NULL)
	{
		if ($color !== NULL)
		{
			$this->set_color($color, self::TYPE_RGB_DEC);
		}
		return $this->_rgb_dec;
	}


	/**
	 * Setter getter via HSL space (array)
	 *
	 * @param object $color [optional]
	 * @return
	 */
	public function hsl($color = NULL)
	{
		if ($color !== NULL)
		{
			$this->set_color($color, self::TYPE_HSL);
		}
		return array(
			'hue' => (int) ($this->_hsl['hue'] * self::HUE_MAX),
			'saturation' => (int) ($this->_hsl['saturation'] * self::SATURATION_MAX),
			'luminance' => (int) ($this->_hsl['luminance'] * self::LUMINANCE_MAX),
		);
	}


	/**
	 * Change saturation by delta amount
	 *
	 * @param object $delta
	 * @return
	 */
	public function saturation($delta)
	{
		$saturation = (int) ($this->_hsl['saturation'] * self::SATURATION_MAX);
		$saturation += $delta;
		$saturation = max(min($saturation, self::SATURATION_MAX),0);
		$this->_hsl['saturation'] = $saturation / self::SATURATION_MAX;
		$this->set_color($this->_hsl, self::TYPE_HSL_INTERNAL);
	}


	/**
	 * Set saturation to absolute value
	 *
	 * @param object $delta
	 * @return
	 */
	public function set_saturation($saturation)
	{
		$saturation = max(min($saturation, self::SATURATION_MAX),0);
		$this->_hsl['saturation'] = $saturation / self::SATURATION_MAX;
		$this->set_color($this->_hsl, self::TYPE_HSL_INTERNAL);
	}


	/**
	 * Get current color's saturation
	 * @return
	 */
	public function get_saturation()
	{
		return (int) ($this->_hsl['saturation'] * self::SATURATION_MAX);
	}


	/**
	 * Change luminance
	 *
	 * @param object $delta
	 * @return
	 */
	public function luminance($delta)
	{
		$luminance = (int) ($this->_hsl['luminance'] * self::LUMINANCE_MAX);
		$luminance += $delta;
		$luminance = max(min($luminance, self::LUMINANCE_MAX),0);
		$this->_hsl['luminance'] = $luminance / self::LUMINANCE_MAX;
		$this->set_color($this->_hsl, self::TYPE_HSL_INTERNAL);
	}


	/**
	 * Change luminance
	 *
	 * @param object $delta
	 * @return
	 */
	public function set_luminance($luminance)
	{
		$luminance = max(min($luminance, self::LUMINANCE_MAX),0);
		$this->_hsl['luminance'] = $luminance / self::LUMINANCE_MAX;
		$this->set_color($this->_hsl, self::TYPE_HSL_INTERNAL);
	}


	/**
	 * Get current color's luminance
	 * @return
	 */
	public function get_luminance()
	{
		return (int) ($this->_hsl['luminance'] * self::LUMINANCE_MAX);
	}


	/**
	 * Change hue
	 *
	 * The degrees are circular, so values are normalized.
	 *
	 * @param object $delta
	 * @return
	 */
	public function hue($delta)
	{
		//
		$delta = $delta % self::HUE_MAX;

		$hue = (int) ($this->_hsl['hue'] * self::HUE_MAX);
		$hue += $delta;

		if ($hue > self::HUE_MAX)
		{
			$hue = $hue % self::HUE_MAX;
		}

		if ($hue < 0)
		{
			$hue += self::HUE_MAX;
		}

		$this->_hsl['hue'] = $hue / self::HUE_MAX;
		$this->set_color($this->_hsl, self::TYPE_HSL_INTERNAL);
	}


	/**
	 * Change hue
	 *
	 * The degrees are circular, so values are normalized.
	 *
	 * @param object $delta
	 * @return
	 */
	public function set_hue($hue)
	{
		while ($hue < 0)
		{
			$hue += self::HUE_MAX;
		}

		$hue %= self::HUE_MAX;

		$this->_hsl['hue'] = $hue / self::HUE_MAX;
		$this->set_color($this->_hsl, self::TYPE_HSL_INTERNAL);
	}


	/**
	 * Get current color's hue
	 * @return
	 */
	public function get_hue()
	{
		return (int) ($this->_hsl['hue'] * self::HUE_MAX);
	}


	/**
	 * Returns Black or White value based on which gives the best contrast
	 *
	 * @return
	 */
	public function black_or_white()
	{
		return ($this->_hsl['luminance'] > 0.5) ? '#000' : '#FFF';
	}


	/**
	 * Convert existing RGB_DEC into HSL. Store values in HSL and RBG_HEX
	 *
	 * @return void
	 */
	protected function make_hsl()
	{
		$rgb = $this->_rgb_dec;

		$red = max(min(($rgb['red'] / self::RGB_MAX),1),0);
		$green = max(min(($rgb['green'] / self::RGB_MAX),1),0);
		$blue = max(min(($rgb['blue'] / self::RGB_MAX),1),0);

		$min = min($red, min($green, $blue));
		$max = max($red, max($green, $blue));
		$delta = $max - $min;

		$luminance = ($min + $max) / 2;
		$saturation = 0;
		if ($luminance > 0 && $luminance < 1)
		{
			$saturation = $delta / ($luminance < 0.5 ? (2 * $luminance) : (2 - 2 * $luminance));
		}
		$hue = 0;

		if ($delta > 0)
		{
			if ($max == $red && $max != $green) $hue += ($green - $blue) / $delta;
			if ($max == $green && $max != $blue) $hue += (2 + ($blue - $red) / $delta);
			if ($max == $blue && $max != $red) $hue += (4 + ($red- $green) / $delta);
			$hue /= 6;
		}
		$this->_hsl = array('hue' => $hue, 'saturation' => $saturation, 'luminance' => $luminance);
	}


	/**
	 * Convert existing HSL into RGB values (HEX and DEC) and store them.
	 *
	 * @return void
	 */
	protected function make_rgb()
	{
		$hsl = $this->_hsl;
		$hue = $hsl['hue'];
		$saturation = max(min($hsl['saturation'],1),0);
		$luminance = max(min($hsl['luminance'], 1),0);

		$max = ($luminance <= 0.5) ? $luminance * ($saturation + 1) : $luminance + $saturation - $luminance * $saturation;
		$min = $luminance * 2 - $max;

		$this->_rgb_dec = array(
			'red' => (int) ($this->_hue_to_rgb($min, $max, $hue + 0.33333) * self::RGB_MAX),
			'green' => (int) ($this->_hue_to_rgb($min, $max, $hue) * self::RGB_MAX),
			'blue' => (int) ($this->_hue_to_rgb($min, $max, $hue - 0.33333) * self::RGB_MAX),
		);
		$this->_rgb_hex = $this->rgb_dec_to_hex($this->_rgb_dec);

	}


	/**
	 * Convert hue values to RGB (used by make_rgb() )
	 *
	 * :KLUDGE: Sorry about the weird formatting on the code, especially the nested ternary (EEEK!) but this is how I found it,
	 * and it works, and I didn't want to break it by molding it to our convention, so it's more or less as it was found. It's
	 * an internal private method anyway...
	 *
	 * @param object $min
	 * @param object $max
	 * @param object $hue
	 * @return
	 */
	private function _hue_to_rgb($min, $max, $hue)
	{
		$hue = ($hue < 0) ? $hue + 1 : (($hue > 1) ? $hue - 1 : $hue);
		if ($hue * 6 < 1) return $min + ($max - $min) * $hue * 6;
		if ($hue * 2 < 1) return $max;
		if ($hue * 3 < 2) return $min + ($max - $min) * (0.66666 - $hue) * 6;
		return $min;
	}


	/**
	 * Quick and dirty interpreter for 6 digit and 3 digit hex values (with or without #) to convert into
	 * an array of decimal equivalents.
	 *
	 * @param object $hex
	 * @return
	 */
	private function rgb_hex_to_dex($hex)
	{
		$hex = ltrim($hex, '#');

		// Convert 3-digits to 6
		if (strlen($hex) == 3)
		{
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		return array(
			'red' => hexdec(substr($hex, 0, 2)),
			'green' => hexdec(substr($hex, 2, 2)),
			'blue' => hexdec(substr($hex, 4, 2)),
		);
	}


	/**
	 * Convert internal RGB decimal-value array to a hex color string.
	 *
	 * @param array $rgb
	 * @return
	 */
	private function rgb_dec_to_hex($rgb)
	{
		return strtoupper(
			str_pad(dechex($rgb['red']), 2, '0', STR_PAD_LEFT).
			str_pad(dechex($rgb['green']), 2, '0', STR_PAD_LEFT).
			str_pad(dechex($rgb['blue']), 2, '0', STR_PAD_LEFT)
		);
	}


	/**
	 * Echos a color box. Really not useful in produciton, but great for testing.
	 */
	public function box()
	{
		echo "<div style='height:54px;width:54px;background-color:{$this};color:{$this->black_or_white()};padding:3px;'>{$this}</div>";
	}


}
