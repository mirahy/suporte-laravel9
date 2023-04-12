<div class="title-card" title="{{ $title ?? null}}">
    <a href="{{ $link ?? '#' }}">
        <div class="card {{$classCard ?? null}}">
            <div class="icon-card">
                <span class="span-icone">
                    <i class="{{ $classIcon ?? 'bi bi-building-add'}}"></i>
                </span>
            </div>
            <div class=" links-card {{$classLink ?? null}}">
                <span>
                    {{ $text ?? $link }}
                </span>
            </div>
        </div>
    </a>
</div>