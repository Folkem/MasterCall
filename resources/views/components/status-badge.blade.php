@props(['status'])
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status->badgeClass() }}">
    {{ $status->label() }}
</span>
