<?php

declare(strict_types=1);

namespace NAi\Generator;

use NAi\AiGeneratorInterface;

/**
 * Anthropic Claude 3.5 Sonnet content generator.
 */
final class ClaudeGenerator implements AiGeneratorInterface
{
	private const API_URL = 'https://api.anthropic.com/v1/messages';
	private const MODEL = 'claude-3-5-sonnet-20241022';


	public function __construct(
		private readonly string $apiKey,
		private readonly string $model = self::MODEL,
	) {}


	public function generate(string $prompt, string $context = ''): string
	{
		$body = [
			'model' => $this->model,
			'max_tokens' => 2048,
			'messages' => [
				['role' => 'user', 'content' => $prompt],
			],
		];

		if ($context !== '') {
			$body['system'] = $context;
		}

		$ch = curl_init(self::API_URL);
		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_HTTPHEADER => [
				'x-api-key: ' . $this->apiKey,
				'anthropic-version: 2023-06-01',
				'Content-Type: application/json',
			],
			CURLOPT_POSTFIELDS => json_encode($body),
		]);

		$response = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($code !== 200) {
			throw new \RuntimeException('Claude API error (HTTP ' . $code . '): ' . $response);
		}

		$data = json_decode($response, true);
		return trim($data['content'][0]['text'] ?? '');
	}


	public function getName(): string
	{
		return 'Claude 3.5 Sonnet';
	}
}
