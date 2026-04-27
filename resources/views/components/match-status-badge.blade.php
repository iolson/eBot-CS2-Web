@php
use App\Models\Matchs;
$colors = [
    Matchs::STATUS_NOT_STARTED    => 'bg-gray-700 text-gray-300',
    Matchs::STATUS_STARTING       => 'bg-yellow-900 text-yellow-300',
    Matchs::STATUS_WU_KNIFE       => 'bg-yellow-900 text-yellow-300',
    Matchs::STATUS_KNIFE          => 'bg-yellow-900 text-yellow-300',
    Matchs::STATUS_END_KNIFE      => 'bg-yellow-900 text-yellow-300',
    Matchs::STATUS_WU_1_SIDE      => 'bg-yellow-900 text-yellow-300',
    Matchs::STATUS_FIRST_SIDE     => 'bg-blue-900 text-blue-300',
    Matchs::STATUS_WU_2_SIDE      => 'bg-yellow-900 text-yellow-300',
    Matchs::STATUS_SECOND_SIDE    => 'bg-blue-900 text-blue-300',
    Matchs::STATUS_WU_OT_1_SIDE   => 'bg-yellow-900 text-yellow-300',
    Matchs::STATUS_OT_FIRST_SIDE  => 'bg-blue-900 text-blue-300',
    Matchs::STATUS_WU_OT_2_SIDE   => 'bg-yellow-900 text-yellow-300',
    Matchs::STATUS_OT_SECOND_SIDE => 'bg-blue-900 text-blue-300',
    Matchs::STATUS_END_MATCH      => 'bg-green-900 text-green-300',
    Matchs::STATUS_ARCHIVE        => 'bg-gray-800 text-gray-500',
];
$color = $colors[$match->status] ?? 'bg-gray-700 text-gray-300';
@endphp
<span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium {{ $color }}">
    {{ $match->getStatusText() }}
</span>
