<?php

declare(strict_types=1);

namespace NAi;

/**
 * Common interface for AI content generation providers.
 *
 * Implement this to add support for new AI services.
 */
interface AiGeneratorInterface
{
	/**
	 * Generate text content from a prompt with optional system context.
	 */
	public function generate(string $prompt, string $context = ''): string;


	/**
	 * Provider name for display in settings.
	 */
	public function getName(): string;
}
