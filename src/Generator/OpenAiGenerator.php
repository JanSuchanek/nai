<?php

declare(strict_types=1);

namespace NAi\Generator;

use NAi\AiGeneratorInterface;

/**
 * OpenAI GPT-4o-mini content generator.
 */
final class OpenAiGenerator implements AiGeneratorInterface
{
	private const API_URL = 'https://api.openai.com/v1/chat/completions';
	private const MODEL = 'gpt-4o-mini';


	public function __construct(
		private readonly string $apiKey,
		private readonly string $model = self::MODEL,
	) {}


	public function generate(string $prompt, string $context = ''): string
	{
		$messages = [];
		if ($context !== '') {
			$messages[] = ['role' => 'system', 'content' => $context];
		}
		$messages[] = ['role' => 'user', 'content' => $prompt];

		$ch = curl_init(self::API_URL);
		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_HTTPHEADER => [
				'Authorization: Bearer ' . $this->apiKey,
				'Content-Type: application/json',
			],
			CURLOPT_POSTFIELDS => json_encode([
				'model' => $this->model,
				'messages' => $messages,
				'max_tokens' => 2048,
				'temperature' => 0.7,
			]) ?: '',
		]);

		$response = (string) curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($code !== 200) {
			throw new \RuntimeException('OpenAI API error (HTTP ' . $code . '): ' . $response);
		}

		/** @var array{choices: list<array{message: array{content: string}}>} $data */
		$data = json_decode($response, true);
		return trim((string) ($data['choices'][0]['message']['content'] ?? ''));
	}


	public function getName(): string
	{
		return 'OpenAI GPT-4o-mini';
	}
}
