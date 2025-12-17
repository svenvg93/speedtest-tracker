<?php

namespace App\Notifications\Apprise;

class AppriseMessage
{
    public function __construct(
        public string|array|null $urls,
        public string $title,
        public string $body,
        public string $type = 'info',
        public string $format = 'text',
        public string|array|null $tag = null,
        public string|array|null $tags = null,
    ) {}

    public static function create(): self
    {
        return new self(
            urls: null,
            title: '',
            body: '',
        );
    }

    public function urls(string|array|null $urls): self
    {
        $this->urls = $urls;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function body(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function tag(string|array|null $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function tags(string|array|null $tags): self
    {
        $this->tags = $tags;

        return $this;
    }
}
