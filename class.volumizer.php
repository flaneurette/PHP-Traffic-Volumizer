<?php

/**
 * TrafficPadding - Generate random hidden data to obfuscate traffic volume
 * 
 * Usage:
 *   $padder = new TrafficPadding(50 * 1024, 5 * 1024 * 1024);
 *   echo $padder->generate($htmlContent);
 */

class TrafficPadding {
    
    private int $minSize;
    private int $maxSize;
    private array $methods;
    
    /**
     * @param int $minSize Minimum padding size in bytes (default: 50KB)
     * @param int $maxSize Maximum padding size in bytes (default: 5MB)
     */
    public function __construct(int $minSize = 51200, int $maxSize = 5242880) {
        $this->minSize = $minSize;
        $this->maxSize = $maxSize;
        
        // Available padding methods
        $this->methods = [
            'htmlComment',
            'base64Image',
            'jsonData',
            'cssComment',
            'jsComment',
            'svgHidden',
            'htmlDataAttribute',
            'noscriptContent'
        ];
    }
    
    /**
     * Generate padded content using random method
     */
    public function generate(string $content): string {
        $paddingSize = random_int($this->minSize, $this->maxSize);
        $method = $this->methods[array_rand($this->methods)];
        
        return $content . $this->$method($paddingSize);
    }
    
    /**
     * Generate padded content using multiple random methods
     */
    public function generateMultiple(string $content, int $methodCount = null): string {
        if ($methodCount === null) {
            $methodCount = random_int(2, 4);
        }
        
        $totalSize = random_int($this->minSize, $this->maxSize);
        $sizePerMethod = intdiv($totalSize, $methodCount);
        
        $shuffledMethods = $this->methods;
        shuffle($shuffledMethods);
        
        $padding = '';
        for ($i = 0; $i < $methodCount; $i++) {
            $method = $shuffledMethods[$i % count($shuffledMethods)];
            $padding .= $this->$method($sizePerMethod);
        }
        
        return $content . $padding;
    }
    
    /**
     * HTML comment with random data
     */
    private function htmlComment(int $size): string {
        $data = $this->generateRandomData($size);
        return "\n<!-- " . base64_encode($data) . " -->\n";
    }
    
    /**
     * Hidden Base64 encoded image (1x1 transparent)
     */
    private function base64Image(int $size): string {
        $data = $this->generateRandomData($size);
        return '<img src="data:image/png;base64,' . base64_encode($data) . '" style="display:none;" alt="" />';
    }
    
    /**
     * Hidden JSON-LD structured data
     */
    private function jsonData(int $size): string {
        $data = $this->generateRandomData($size);
        return '<script type="application/ld+json">' . 
               json_encode(['@context' => 'https://schema.org', 'padding' => base64_encode($data)]) . 
               '</script>';
    }
    
    /**
     * CSS comment block
     */
    private function cssComment(int $size): string {
        $data = $this->generateRandomData($size);
        return '<style>/* ' . base64_encode($data) . ' */</style>';
    }
    
    /**
     * JavaScript comment block
     */
    private function jsComment(int $size): string {
        $data = $this->generateRandomData($size);
        return '<script>/* ' . base64_encode($data) . ' */</script>';
    }
    
    /**
     * Hidden SVG with random data
     */
    private function svgHidden(int $size): string {
        $data = $this->generateRandomData($size);
        return '<svg style="display:none;"><desc>' . base64_encode($data) . '</desc></svg>';
    }
    
    /**
     * Hidden data attribute on div
     */
    private function htmlDataAttribute(int $size): string {
        $data = $this->generateRandomData($size);
        return '<div style="display:none;" data-padding="' . base64_encode($data) . '"></div>';
    }
    
    /**
     * Noscript tag with hidden content
     */
    private function noscriptContent(int $size): string {
        $data = $this->generateRandomData($size);
        return '<noscript>' . base64_encode($data) . '</noscript>';
    }
    
    /**
     * Generate cryptographically secure random bytes
     */
    private function generateRandomData(int $size): string {
        // Reduce size slightly to account for base64 encoding overhead
        $rawSize = intdiv($size * 3, 4);
        return random_bytes($rawSize);
    }
    
    /**
     * Set custom size range
     */
    public function setRange(int $minSize, int $maxSize): void {
        $this->minSize = $minSize;
        $this->maxSize = $maxSize;
    }
    
    /**
     * Get current configuration
     */
    public function getConfig(): array {
        return [
            'minSize' => $this->minSize,
            'maxSize' => $this->maxSize,
            'minSizeKB' => round($this->minSize / 1024, 2),
            'maxSizeKB' => round($this->maxSize / 1024, 2),
            'availableMethods' => count($this->methods)
        ];
    }
}
?>
