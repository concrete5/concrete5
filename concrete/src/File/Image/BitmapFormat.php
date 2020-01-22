<?php

namespace Concrete\Core\File\Image;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Utility\Service\Validation\Numbers;

/**
 * Helper class for bitmap image formats.
 */
class BitmapFormat
{
    /**
     * Bitmap image format: PNG.
     *
     * @var string
     */
    const FORMAT_PNG = 'png';

    /**
     * Bitmap image format: JPEG.
     *
     * @var string
     */
    const FORMAT_JPEG = 'jpeg';

    /**
     * Bitmap image format: GIF.
     *
     * @var string
     */
    const FORMAT_GIF = 'gif';

    /**
     * Bitmap image format: WBMP.
     *
     * @var string
     */
    const FORMAT_WBMP = 'wbmp';

    /**
     * Bitmap image format: WEBP.
     *
     * @var string
     */
    const FORMAT_WEBP = 'webp';

    /**
     * Bitmap image format: XBM.
     *
     * @var string
     */
    const FORMAT_XBM = 'xbm';

    /**
     * Default JPEG quality (from 0 to 100) - to be used when there's no (valid) configured value.
     *
     * @var int
     */
    const DEFAULT_JPEG_QUALITY = 80;

    /**
     * Default PNG compression level (from 0 to 9) - to be used when there's no (valid) configured value.
     *
     * @var int
     */
    const DEFAULT_PNG_COMPRESSIONLEVEL = 9;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Concrete\Core\Utility\Service\Validation\Numbers
     */
    protected $valn;

    /**
     * All the image formats (if initialized).
     *
     * @var string[]|null
     */
    protected $allImageFormats;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Config\Repository\Repository $config
     * @param \Concrete\Core\Utility\Service\Validation\Numbers $valn
     */
    public function __construct(Repository $config, Numbers $valn)
    {
        $this->config = $config;
        $this->valn = $valn;
    }

    /**
     * Get all the image formats.
     *
     * @return string[]
     */
    public function getAllImageFormats()
    {
        if ($this->allImageFormats === null) {
            $this->allImageFormats = [
                static::FORMAT_PNG,
                static::FORMAT_JPEG,
                static::FORMAT_GIF,
                static::FORMAT_WBMP,
                static::FORMAT_WEBP,
                static::FORMAT_XBM,
            ];
        }

        return $this->allImageFormats;
    }

    /**
     * Check if a format identifier is valid.
     *
     * @param string|mixed $format
     *
     * @return bool
     */
    public function isFormatValid($format)
    {
        return in_array($format, $this->getAllImageFormats(), true);
    }

    /**
     * Get the MIME type for a specific format.
     *
     * @param string $format One of the BitmapFormat::FORMAT_...  constants.
     *
     * @return string return an empty string is $format is invalid
     */
    public function getFormatMimeType($format)
    {
        $format = $this->normalizeFormat($format);
        $result = '';
        switch ($format) {
            case static::FORMAT_PNG:
            case static::FORMAT_JPEG:
            case static::FORMAT_GIF:
            case static::FORMAT_XBM:
            case static::FORMAT_WEBP:
                $result = 'image/' . $format;
                break;
            case static::FORMAT_WBMP:
                $result = 'image/vnd.wap.wbmp';
                break;
        }

        return $result;
    }

    /**
     * Get the bitmap format corresponding to a specific MIME type.
     *
     * @param string $mimeType the MIME type to analyze
     * @param mixed $fallback what to return if $mimeType is not recognized
     *
     * @return string|mixed
     */
    public function getFormatFromMimeType($mimeType, $fallback = '')
    {
        $mimeType = trim(strtolower((string) $mimeType));
        switch ($mimeType) {
            case 'image/png':
                $result = static::FORMAT_PNG;
                break;
            case 'image/jpeg':
                $result = static::FORMAT_JPEG;
                break;
            case 'image/gif':
                $result = static::FORMAT_GIF;
                break;
            case 'image/webp':
                $result = static::FORMAT_WEBP;
                break;
            case 'image/vnd.wap.wbmp':
                $result = static::FORMAT_WBMP;
                break;
            case 'image/xbm':
            case 'image/x-xbm':
            case 'image/x-xbitmap':
                $result = static::FORMAT_XBM;
                break;
            default:
                $result = $fallback;
                break;
        }

        return $result;
    }

    /**
     * Get the Imagine save options for the specified image format.
     *
     * @param string $format One of the BitmapFormat::FORMAT_...  constants.
     *
     * @return array
     */
    public function getFormatImagineSaveOptions($format)
    {
        $format = $this->normalizeFormat($format);
        $result = [
            'format' => $format,
        ];
        switch ($format) {
            case static::FORMAT_PNG:
                $result['png_compression_level'] = $this->getDefaultPngCompressionLevel();
                break;
            case static::FORMAT_JPEG:
                $result['jpeg_quality'] = $this->getDefaultJpegQuality();
                break;
        }

        return $result;
    }

    /**
     * Get the file extension for the specified format.
     *
     * @param string $format One of the BitmapFormat::FORMAT_...  constants.
     *
     * @return string returns an empty string if $format is invalid
     *
     * @example $bitmapFormat->getFormatFileExtension(BitmapFormat::FORMAT_GIF) === 'gif'
     */
    public function getFormatFileExtension($format)
    {
        $format = $this->normalizeFormat($format);
        switch ($format) {
            case static::FORMAT_PNG:
                $result = 'png';
                break;
            case static::FORMAT_JPEG:
                $result = 'jpg';
                break;
            case static::FORMAT_GIF:
                $result = 'gif';
                break;
            case static::FORMAT_WEBP:
                $result = 'webp';
                break;
            case static::FORMAT_WBMP:
                $result = 'wbmp';
                break;
            case static::FORMAT_XBM:
                $result = 'xbm';
                break;
            default:
                $result = '';
        }

        return $result;
    }

    /**
     * Set the default JPEG quality.
     *
     * @param int $value an integer from 0 to 100
     *
     * @return $this
     */
    public function setDefaultJpegQuality($value)
    {
        if ($this->valn->integer($value, 0, 100)) {
            $value = (int) $value;
            $this->config->set('concrete.misc.default_jpeg_image_compression', $value);
            $this->config->save('concrete.misc.default_jpeg_image_compression', $value);
        }

        return $this;
    }

    /**
     * Get the default JPEG quality.
     *
     * @return int an integer from 0 to 100
     */
    public function getDefaultJpegQuality()
    {
        $result = $this->config->get('concrete.misc.default_jpeg_image_compression');
        if ($this->valn->integer($result, 0, 100)) {
            $result = (int) $result;
        } else {
            $result = static::DEFAULT_JPEG_QUALITY;
        }

        return $result;
    }

    /**
     * Set the default PNG compression level.
     *
     * @param int $value an integer from 0 to 9
     *
     * @return $this
     */
    public function setDefaultPngCompressionLevel($value)
    {
        if ($this->valn->integer($value, 0, 9)) {
            $value = (int) $value;
            $this->config->set('concrete.misc.default_png_image_compression', $value);
            $this->config->save('concrete.misc.default_png_image_compression', $value);
        }

        return $this;
    }

    /**
     * Get the default PNG compression level.
     *
     * @return int an integer from 0 to 9
     */
    public function getDefaultPngCompressionLevel()
    {
        $result = $this->config->get('concrete.misc.default_png_image_compression');
        if ($this->valn->integer($result, 0, 9)) {
            $result = (int) $result;
        } else {
            $result = static::DEFAULT_PNG_COMPRESSIONLEVEL;
        }

        return $result;
    }

    /**
     * Normalize a format.
     *
     * @param string|mixed $format
     *
     * @return string
     */
    protected function normalizeFormat($format)
    {
        $format = strtolower(trim((string) $format));
        if ($format === 'jpg') {
            $format = static::FORMAT_JPEG;
        }

        return $format;
    }
}
