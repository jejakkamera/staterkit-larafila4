<?php

namespace App\Livewire\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use SplFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogViewer extends Component
{
    public array $logFiles = [];
    public string $selectedLog = 'laravel.log';
    public string $search = '';
    public $lineLimit = 50;
    public array $entries = [];
    public ?string $statusMessage = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $page = 1;
    public bool $hasMore = false;

    private int $maxLineLimit = 1000;

    public function mount(): void
    {
        $this->loadLogFiles();
        $this->ensureSelectedLogExists();
        $this->loadEntries();
    }

    public function updatedSelectedLog(): void
    {
        $this->page = 1;
        $this->loadEntries();
    }

    public function updatedSearch(): void
    {
        $this->page = 1;
        $this->loadEntries();
    }

    public function updatedLineLimit(): void
    {
        $this->lineLimit = (int) $this->lineLimit;
        $this->lineLimit = max(50, min($this->lineLimit, $this->maxLineLimit));
        $this->page = 1;
        $this->loadEntries();
    }

    public function updatedStartDate(): void
    {
        $this->page = 1;
        $this->loadEntries();
    }

    public function updatedEndDate(): void
    {
        $this->page = 1;
        $this->loadEntries();
    }

    public function refreshLogs(): void
    {
        $this->loadEntries();
        $this->statusMessage = __('Logs refreshed');
    }

    public function nextPage(): void
    {
        if (! $this->hasMore) {
            return;
        }

        $this->page++;
        $this->loadEntries();
    }

    public function previousPage(): void
    {
        if ($this->page === 1) {
            return;
        }

        $this->page--;
        $this->loadEntries();
    }

    public function resetLog(): void
    {
        $path = $this->selectedLogPath();

        if (! File::exists($path)) {
            $this->statusMessage = __('Log file not found.');
            return;
        }

        File::put($path, '');
        $this->statusMessage = __('Log file has been reset.');
        $this->page = 1;
        $this->loadEntries();
    }

    public function deleteLog(): void
    {
        $path = $this->selectedLogPath();

        if (File::exists($path)) {
            File::delete($path);
        }

        $this->statusMessage = __('Log file deleted.');
        $this->loadLogFiles();
        $this->ensureSelectedLogExists();
        $this->page = 1;
        $this->loadEntries();
    }

    public function download(): StreamedResponse
    {
        $path = $this->selectedLogPath();

        abort_unless(File::exists($path), 404);

        return response()->streamDownload(function () use ($path) {
            $stream = fopen($path, 'r');
            while (!feof($stream)) {
                echo fread($stream, 8192);
            }
            fclose($stream);
        }, $this->selectedLog);
    }

    private function loadLogFiles(): void
    {
        $logPath = storage_path('logs');
        $files = File::exists($logPath) ? File::files($logPath) : [];

        $this->logFiles = collect($files)
            ->filter(fn ($file) => Str::endsWith($file->getFilename(), '.log'))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => $file->getFilename())
            ->values()
            ->all();

        $this->ensureSelectedLogExists();
    }

    private function ensureSelectedLogExists(): void
    {
        if (! in_array($this->selectedLog, $this->logFiles, true)) {
            $this->selectedLog = $this->logFiles[0] ?? 'laravel.log';
        }
    }

    private function loadEntries(): void
    {
        $path = $this->selectedLogPath();

        if (! File::exists($path)) {
            $this->entries = [];
            $this->hasMore = false;
            return;
        }

        [$start, $end] = $this->resolveDateFilters();
        $search = $this->search !== '' ? Str::lower($this->search) : null;

        [$lines, $hasMore] = $this->readLines($path, $this->lineLimit, $this->page, $start, $end, $search);
        $this->hasMore = $hasMore;

        $this->entries = collect($lines)
            ->map(fn ($line) => $this->parseLine($line))
            ->values()
            ->all();
    }

    private function parseLine(string $line): array
    {
        $entry = [
            'timestamp' => null,
            'level' => 'info',
            'message' => trim($line),
        ];

        if (preg_match('/\[(?<date>.+?)\]\s(?<channel>[\w\.\-]+)\.(?<level>[A-Z]+):\s(?<message>.*)/', $line, $matches)) {
            $entry['timestamp'] = $matches['date'];
            $entry['level'] = Str::lower($matches['level']);
            $entry['message'] = trim($matches['message']);
            $entry['channel'] = $matches['channel'];
        }

        return $entry;
    }

    private function selectedLogPath(): string
    {
        return storage_path('logs/' . $this->selectedLog);
    }

    private function readLines(
        string $path,
        int $perPage,
        int $page,
        ?Carbon $start,
        ?Carbon $end,
        ?string $search
    ): array {
        $file = new SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $position = $file->key();
        $skip = max(0, ($page - 1) * $perPage);
        $lines = [];
        $hasMore = false;

        while ($position >= 0) {
            $file->seek($position);
            $line = rtrim($file->current(), "\r\n");
            $position--;

            if ($line === '') {
                continue;
            }

            if (! $this->lineMatchesFilters($line, $start, $end, $search)) {
                continue;
            }

            if ($skip > 0) {
                $skip--;
                continue;
            }

            $lines[] = $line;

            if (count($lines) === $perPage) {
                while ($position >= 0) {
                    $file->seek($position);
                    $nextLine = rtrim($file->current(), "\r\n");
                    $position--;

                    if ($nextLine === '') {
                        continue;
                    }

                    if ($this->lineMatchesFilters($nextLine, $start, $end, $search)) {
                        $hasMore = true;
                        break 2;
                    }
                }

                break;
            }
        }

        return [$lines, $hasMore];
    }

    private function lineMatchesFilters(string $line, ?Carbon $start, ?Carbon $end, ?string $search): bool
    {
        $timestamp = $this->extractTimestamp($line);

        if ($start && $timestamp && $timestamp->lt($start)) {
            return false;
        }

        if ($end && $timestamp && $timestamp->gt($end)) {
            return false;
        }

        if ($search && ! Str::contains(Str::lower($line), $search)) {
            return false;
        }

        return true;
    }

    private function extractTimestamp(string $line): ?Carbon
    {
        if (preg_match('/\[(?<date>[^\]]+)\]/', $line, $matches)) {
            try {
                return Carbon::parse($matches['date']);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    private function resolveDateFilters(): array
    {
        $start = null;
        $end = null;

        if ($this->startDate) {
            try {
                $start = Carbon::parse($this->startDate)->startOfDay();
            } catch (\Throwable $e) {
                $this->startDate = null;
            }
        }

        if ($this->endDate) {
            try {
                $end = Carbon::parse($this->endDate)->endOfDay();
            } catch (\Throwable $e) {
                $this->endDate = null;
            }
        }

        if ($start && $end && $start->gt($end)) {
            $end = $start->copy()->endOfDay();
            $this->endDate = $start->format('Y-m-d');
        }

        return [$start, $end];
    }

    public function render()
    {
        return view('livewire.admin.log-viewer');
    }
}
