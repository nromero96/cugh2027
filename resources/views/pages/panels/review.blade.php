@php
    $criteria = [
        1 => ['Relevance to Conference Track', 'Does the panel clearly align with one of the six conference tracks?'],
        2 => ['Clarity of the Panel Description', 'Is the panel description clear and are the panel’s goals well-defined, focused, and achievable within the session?'],
        3 => ['Topical Relevance & Innovation', 'Does the panel address a current or emerging global health issue or innovation?'],
        4 => ['Potential to Inform Knowledge, Policy or Outcomes', 'Is the panel solution-oriented, focused on actionable ideas that could result in impact and outcomes that could inform policy or practice?'],
        5 => ['Diversity and Inclusion of Panelists', 'Does the panel include a diversity of speakers (e.g., geography, gender, institution, discipline)? Does it include at least one speaker from a low- or middle-income country (LMIC)?'],
        6 => ['Expertise and Complementarity of Speakers', 'Are the speakers qualified and does the proposal clearly demonstrate how their expertise complements one another?'],
        7 => ['Feasibility within the Panel Format', 'Is the proposed content well-suited for the 75-minute format (presentations, discussion, audience Q&A)?'],
        8 => ['Overall Quality and Impact', 'Is the proposal well-written, organized, and compelling? Does it stand out as innovative or likely to engage the audience?'],
    ];
@endphp

<style>
#panel-evaluation { border: 1px solid #e7eaf3; border-radius: 14px; overflow: hidden; }
#panel-evaluation .evaluation-heading { background: linear-gradient(135deg, #f4f7ff 0%, #eef8ff 100%); border-bottom: 1px solid #e3e8f2; }
#panel-evaluation .score-legend-item { height: 100%; padding: 12px 14px; border: 1px solid transparent; border-radius: 10px; }
#panel-evaluation .score-legend-item strong, #panel-evaluation .score-legend-item span { display: block; }
#panel-evaluation .score-legend-item span { margin-top: 2px; font-size: 1.05rem; font-weight: 700; }
#panel-evaluation .score-legend-poor { background: #fff5f5; border-color: #ffd6d6; color: #b42318; }
#panel-evaluation .score-legend-fair { background: #fff9eb; border-color: #ffe4a8; color: #946200; }
#panel-evaluation .score-legend-good { background: #eef8ff; border-color: #cce9ff; color: #1261a0; }
#panel-evaluation .score-legend-excellent { background: #effaf3; border-color: #cdeed8; color: #16733c; }
#panel-evaluation .evaluation-criterion { padding: 16px; background: #fff; border: 1px solid #e5e9f2; border-radius: 12px; transition: border-color .2s ease, box-shadow .2s ease; }
#panel-evaluation .evaluation-criterion:focus-within { border-color: #6f8cff; box-shadow: 0 0 0 3px rgba(111, 140, 255, .12); }
#panel-evaluation .criterion-number { display: inline-flex; flex: 0 0 34px; align-items: center; justify-content: center; width: 34px; height: 34px; margin-right: 12px; color: #fff; background: #4361ee; border-radius: 50%; font-weight: 700; }
#panel-evaluation .criterion-score-input { max-width: 135px; margin-left: auto; }
#panel-evaluation .criterion-score-input .form-control { min-height: 46px; font-size: 1.15rem; font-weight: 700; text-align: center; }
#panel-evaluation .evaluation-summary { padding: 14px 16px; background: #f7f9fc; border: 1px solid #e4e9f1; border-radius: 12px; }
@media (max-width: 767.98px) { #panel-evaluation .criterion-score-input { max-width: none; margin-top: 12px; } }
</style>

@if(auth()->user()->hasRole('Administrador'))
    @php
        $completed = $panel->reviewers->filter(function ($reviewer) { return $reviewer->pivot->average_score !== null; });
        $combined = $completed->sum(function ($reviewer) { return collect(range(1, 8))->sum(function ($number) use ($reviewer) { return (int) $reviewer->pivot->{'score_'.$number}; }); });
        $averageHundredths = $completed->count() ? intdiv($combined * 100, $completed->count() * 8) : null;
    @endphp
    <div class="statbox widget box box-shadow mt-3 no-print"><div class="widget-header py-3 px-3"><h5 class="mb-1">Review Results</h5><small class="text-muted">Evaluation scores and notes are visible to administrators only.</small></div><div class="widget-content widget-content-area pt-0">
        @if($panel->reviewers->isEmpty())<div class="alert alert-info mb-0">No reviewers have been assigned to this panel.</div>
        @else
            <p><span class="badge {{ $completed->count() === $panel->reviewers->count() ? 'badge-light-success' : 'badge-light-warning' }}">{{ $completed->count() }} / {{ $panel->reviewers->count() }} evaluations</span>
                @if($averageHundredths !== null)<strong class="ms-2">{{ $completed->count() === $panel->reviewers->count() ? 'Overall' : 'Partial' }} average: {{ sprintf('%d.%02d', intdiv($averageHundredths, 100), $averageHundredths % 100) }} / 10</strong>@endif</p>
            <div class="table-responsive"><table class="table table-bordered table-hover align-middle"><thead><tr><th>Reviewer</th>@foreach($criteria as $number => $criterion)<th class="text-center" title="{{ $criterion[0] }}">{{ $number }}</th>@endforeach<th class="text-center">Total / 80</th><th class="text-center">Average / 10</th><th>Note</th></tr></thead><tbody>
                @foreach($panel->reviewers as $reviewer)
                    @php $submitted = $reviewer->pivot->average_score !== null; $sum = 0; @endphp
                    <tr><td><strong>{{ trim($reviewer->name.' '.$reviewer->lastname.' '.$reviewer->second_lastname) }}</strong><small class="d-block text-muted">{{ $reviewer->email }}</small></td>
                    @foreach($criteria as $number => $criterion)@php $score = $reviewer->pivot->{'score_'.$number}; $sum += (int) $score; @endphp<td class="text-center" title="{{ $criterion[0] }}">{{ $submitted ? $score : '—' }}</td>@endforeach
                    <td class="text-center">{{ $submitted ? $sum : '—' }}</td><td class="text-center">{{ $submitted ? number_format($reviewer->pivot->average_score, 2) : 'Pending' }}</td><td style="min-width:180px;white-space:pre-wrap">{{ $submitted ? ($reviewer->pivot->reviewer_note ?: '—') : '—' }}</td></tr>
                @endforeach
            </tbody></table></div>
            <small class="text-muted">1. Relevance · 2. Clarity · 3. Innovation · 4. Outcomes · 5. Diversity · 6. Expertise · 7. Feasibility · 8. Overall quality</small>
        @endif
    </div></div>
@endif

@if($reviewAssignment)
    @php $locked = in_array($panel->status, ['Qualified', 'Rejected'], true) || $reviewAssignment->pivot->average_score !== null; @endphp
    <div class="statbox widget box box-shadow mt-3 no-print" id="panel-evaluation"><div class="widget-header evaluation-heading py-3 px-3"><h5 class="mb-1">Panel Evaluation</h5><small class="text-muted">Eight criteria, each scored 1–10. Maximum total score: 80.</small></div><div class="widget-content widget-content-area pt-0">
        @if($locked)<div class="alert alert-info">{{ $reviewAssignment->pivot->average_score !== null ? 'Your evaluation has been submitted and can no longer be changed.' : 'Evaluation is closed for this panel.' }}</div>@endif
        <div class="row g-2 mb-4 mt-2"><div class="col-6 col-lg-3"><div class="score-legend-item score-legend-poor"><strong>Poor</strong><span>1–3</span></div></div><div class="col-6 col-lg-3"><div class="score-legend-item score-legend-fair"><strong>Fair</strong><span>4–6</span></div></div><div class="col-6 col-lg-3"><div class="score-legend-item score-legend-good"><strong>Good</strong><span>7–8</span></div></div><div class="col-6 col-lg-3"><div class="score-legend-item score-legend-excellent"><strong>Excellent</strong><span>9–10</span></div></div></div>
        <form method="POST" action="{{ route('panels.review', $panel) }}" id="panel-review-form">@csrf @method('PUT')
            <div class="row g-3">@foreach($criteria as $number => $criterion)
                @php $field = 'score_'.$number; @endphp
                <div class="col-12"><div class="evaluation-criterion"><div class="row align-items-center g-0"><div class="col-md"><label class="d-flex align-items-start mb-0" for="panel-{{ $field }}"><span class="criterion-number">{{ $number }}</span><span><strong class="d-block text-dark">{{ $criterion[0] }}</strong><span class="d-block text-muted">{{ $criterion[1] }}</span><small class="text-muted">Maximum score: 10</small></span></label></div><div class="col-md-auto criterion-score-input"><div class="input-group"><input id="panel-{{ $field }}" name="{{ $field }}" type="number" min="1" max="10" step="1" inputmode="numeric" class="form-control panel-score @error($field) is-invalid @enderror" value="{{ old($field, $reviewAssignment->pivot->{$field}) }}" {{ $locked ? 'readonly' : 'required' }}><span class="input-group-text">/ 10</span></div></div></div></div></div>
            @endforeach
                <div class="col-12"><div class="evaluation-summary d-flex flex-wrap gap-4"><strong>Maximum total: 80</strong><span>Total: <strong id="panel-review-total">—</strong></span><span>Average: <strong id="panel-review-average">{{ $reviewAssignment->pivot->average_score !== null ? number_format($reviewAssignment->pivot->average_score, 2) : '—' }}</strong></span></div></div>
                <div class="col-12"><label for="panel-reviewer-note" class="form-label">Review note <span class="text-muted">(optional)</span></label><textarea id="panel-reviewer-note" name="reviewer_note" class="form-control @error('reviewer_note') is-invalid @enderror" rows="4" maxlength="5000" {{ $locked ? 'readonly' : '' }}>{{ old('reviewer_note', $reviewAssignment->pivot->reviewer_note) }}</textarea></div>
                @unless($locked)<div class="col-12 text-end"><button type="submit" class="btn btn-primary">Save Evaluation</button></div>@endunless
            </div>
        </form>
    </div></div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('panel-review-form');
        const inputs = Array.from(form.querySelectorAll('.panel-score'));
        function update() {
            const scores = inputs.map(function (input) { return input.value.trim() === '' ? NaN : Number(input.value); });
            const valid = scores.length === 8 && scores.every(function (score) { return Number.isInteger(score) && score >= 1 && score <= 10; });
            const total = valid ? scores.reduce(function (sum, score) { return sum + score; }, 0) : null;
            document.getElementById('panel-review-total').textContent = total === null ? '—' : total + ' / 80';
            document.getElementById('panel-review-average').textContent = total === null ? '—' : (Math.trunc(total * 100 / 8) / 100).toFixed(2);
        }
        inputs.forEach(function (input) { input.addEventListener('input', update); });
        form.addEventListener('submit', function (event) { if (!window.confirm('Submit this evaluation? Once submitted, you will not be able to change your scores or review note.')) event.preventDefault(); });
        update();
    });
    </script>
@endif
