@extends('layouts.app')

@section('title', 'Form Penilaian Kinerja')

@push('styles')
<!-- Tailwind CSS via CDN with prefix to avoid conflict with Bootstrap -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        prefix: 'tw-',
        corePlugins: { preflight: false },
        theme: {
            extend: {
                colors: {
                    primary: '#0ea5e9', // Sky 500
                    'primary-dark': '#0284c7', // Sky 600
                },
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                }
            }
        }
    }
</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* Prevent bootstrap conflicting with body font */
    #assessment-app-container {
        font-family: 'Inter', sans-serif;
    }
    
    /* Glassmorphism utility */
    .tw-glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    
    /* Custom Slider Range */
    input[type=range].custom-range {
        -webkit-appearance: none;
        width: 100%;
        background: transparent;
        position: relative;
        z-index: 10;
    }
    input[type=range].custom-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 24px;
        width: 24px;
        border-radius: 50%;
        background: #0ea5e9;
        cursor: pointer;
        margin-top: -9px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 3px solid white;
        transition: transform 0.15s ease;
    }
    input[type=range].custom-range::-webkit-slider-thumb:hover {
        transform: scale(1.2);
    }
    input[type=range].custom-range::-webkit-slider-runnable-track {
        width: 100%;
        height: 6px;
        cursor: pointer;
        background: #e2e8f0;
        border-radius: 9999px;
    }
    input[type=range].custom-range:focus {
        outline: none;
    }
    
    /* Tab transitions */
    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    .tab-content.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .rating-number {
        transition: all 0.2s ease;
    }
    .rating-number.active {
        background-color: #0ea5e9 !important;
        color: white !important;
        transform: scale(1.15);
        box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3);
    }
    
    /* Custom scrollbar for tabs */
    .custom-scrollbar::-webkit-scrollbar {
        height: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div id="assessment-app-container" class="tw-font-sans tw-w-full tw-bg-slate-50 tw-p-2 md:tw-p-6 tw-rounded-xl tw-min-h-[85vh]">
    
    <!-- Header Modal-like Container -->
    <div class="tw-max-w-6xl tw-mx-auto tw-glass tw-rounded-2xl tw-shadow-xl tw-overflow-hidden tw-border tw-border-slate-200/60">
        
        <!-- Header Section -->
        <div class="tw-bg-white/80 tw-px-6 md:tw-px-10 tw-py-8 tw-border-b tw-border-slate-200">
            <h1 class="tw-text-3xl tw-font-bold tw-text-slate-800 tw-tracking-tight tw-mb-0">Mulai Penilaian Pegawai</h1>
            <p class="tw-text-slate-500 tw-mt-2 tw-mb-0">Evaluasi 360° untuk <span class="tw-font-semibold tw-text-primary">{{ $target->name }}</span> ({{ $target->nip }}) &nbsp;•&nbsp; Tipe: {{ $type }}</p>
        </div>

        <!-- Form Start -->
        <form action="{{ route('transaction.assessments.store') }}" method="POST" id="assessmentForm" class="tw-relative tw-m-0">
            @csrf
            <input type="hidden" name="target_id" value="{{ $target->id }}">
            <input type="hidden" name="type" value="{{ $type }}">
            
            <!-- Progress Bar -->
            <div class="tw-bg-slate-100 tw-h-1.5 tw-w-full">
                <div id="progressBar" class="tw-bg-primary tw-h-1.5 tw-transition-all tw-duration-500 tw-ease-out" style="width: 0%"></div>
            </div>

            <div class="tw-flex tw-flex-col md:tw-flex-row tw-h-full">
                
                <!-- Sidebar / Tabs (Horizontal on mobile, vertical or horizontal on desktop) -->
                <div class="tw-w-full tw-border-b tw-border-slate-200 tw-bg-white/50 tw-overflow-x-auto custom-scrollbar">
                    <div class="tw-flex tw-flex-nowrap tw-min-w-max tw-p-2 tw-gap-2">
                        @foreach($categories as $index => $category)
                            <button type="button" 
                                    class="category-tab tw-px-5 tw-py-3 tw-rounded-xl tw-text-sm tw-font-medium tw-transition-all tw-duration-200 tw-whitespace-nowrap tw-border-0
                                    {{ $index === 0 ? 'tw-bg-primary tw-text-white tw-shadow-md' : 'tw-bg-transparent tw-text-slate-600 hover:tw-bg-slate-100' }}"
                                    data-target="tab-{{ $index }}">
                                {{ $category->name }}
                                <span class="tab-badge tw-ml-2 tw-text-xs tw-px-2 tw-py-0.5 tw-rounded-full {{ $index === 0 ? 'tw-bg-white/20' : 'tw-bg-slate-200' }}" id="badge-{{ $index }}">0/{{ count($category->indicators) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="tw-p-4 md:tw-p-10 tw-bg-slate-50/50">
                
                <div class="tw-mb-6 tw-flex tw-justify-between tw-items-center">
                    <h2 id="currentCategoryTitle" class="tw-text-xl tw-font-semibold tw-text-slate-700 tw-m-0">{{ $categories[0]->name ?? 'Kategori' }}</h2>
                    <div class="tw-text-sm tw-font-medium tw-text-slate-500 tw-bg-white tw-px-4 tw-py-2 tw-rounded-full tw-shadow-sm tw-border tw-border-slate-100">
                        Kategori <span id="currentCategoryIndex">1</span> dari {{ count($categories) }} &nbsp;|&nbsp; Progress <span id="progressText">0%</span>
                    </div>
                </div>

                @foreach($categories as $catIndex => $category)
                    <div id="tab-{{ $catIndex }}" class="tab-content {{ $catIndex === 0 ? 'active' : '' }}">
                        <div class="tw-space-y-6">
                            @foreach($category->indicators as $indIndex => $indicator)
                                <div class="tw-bg-white tw-rounded-2xl tw-p-6 md:tw-p-8 tw-shadow-sm tw-border tw-border-slate-100 hover:tw-shadow-md tw-transition-shadow">
                                    
                                    <div class="tw-flex tw-gap-4 tw-mb-8">
                                        <div class="tw-flex-shrink-0 tw-w-10 tw-h-10 tw-bg-slate-100 tw-text-slate-600 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-font-bold tw-text-lg">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="tw-pt-1">
                                            <p class="tw-text-lg tw-text-slate-800 tw-leading-relaxed tw-font-medium tw-m-0">
                                                {{ $indicator->name ?? $indicator->question ?? $indicator->indicator }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Interactive Scoring Component -->
                                    <div class="tw-pl-0 md:tw-pl-14">
                                        <div class="tw-flex tw-justify-between tw-mb-4 tw-px-1">
                                            @for($i = 1; $i <= 10; $i++)
                                                <button type="button" 
                                                        class="rating-number tw-w-8 tw-h-8 md:tw-w-10 md:tw-h-10 tw-rounded-full tw-bg-slate-100 tw-text-slate-600 tw-font-semibold tw-text-sm md:tw-text-base tw-flex tw-items-center tw-justify-center hover:tw-bg-slate-200 tw-border-0"
                                                        data-value="{{ $i }}"
                                                        data-input="score_{{ $indicator->id }}">
                                                    {{ $i }}
                                                </button>
                                            @endfor
                                        </div>
                                        
                                        <div class="tw-relative tw-w-full tw-h-6 tw-flex tw-items-center">
                                            <!-- Colored track overlay -->
                                            <div class="tw-absolute tw-left-0 tw-h-1.5 tw-bg-primary tw-rounded-full tw-pointer-events-none tw-z-0" 
                                                 id="track_{{ $indicator->id }}" 
                                                 style="width: 0%; display: none;"></div>
                                                 
                                            <!-- The actual input that gets submitted -->
                                            <input type="range" min="1" max="10" step="1" 
                                                   class="custom-range tw-w-full form-score-input" 
                                                   name="scores[{{ $indicator->id }}]" 
                                                   id="score_{{ $indicator->id }}" 
                                                   value="1" 
                                                   data-category="{{ $catIndex }}"
                                                   data-answered="false"
                                                   required>
                                        </div>
                                        <div class="tw-flex tw-justify-between tw-text-xs tw-text-slate-400 tw-mt-2 tw-font-medium tw-px-1">
                                            <span>1 - Sangat Kurang</span>
                                            <span>10 - Sangat Baik</span>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- General Notes -->
                <div id="tab-notes" class="tab-content tw-mt-6">
                    <div class="tw-bg-white tw-rounded-2xl tw-p-6 md:tw-p-8 tw-shadow-sm tw-border tw-border-slate-100">
                        <h3 class="tw-text-lg tw-font-semibold tw-text-slate-800 tw-mb-4">Catatan Tambahan (Opsional)</h3>
                        <textarea name="general_notes" rows="4" class="tw-w-full tw-rounded-xl tw-border tw-border-slate-200 tw-p-4 focus:tw-border-primary focus:tw-ring focus:tw-ring-primary/20 tw-transition-all tw-bg-slate-50 focus:tw-bg-white" placeholder="Tuliskan apresiasi, masukan, atau saran perbaikan untuk pengembangan kinerja pegawai ini..."></textarea>
                    </div>
                </div>

            </div>

            <!-- Footer Action Buttons -->
            <div class="tw-bg-white tw-border-t tw-border-slate-200 tw-p-6 md:tw-p-8 tw-flex tw-flex-col-reverse md:tw-flex-row tw-gap-4 md:tw-justify-between tw-items-center">
                
                <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3 tw-w-full md:tw-w-auto">
                    <button type="button" id="btnPrev" class="tw-hidden tw-border-0 tw-px-6 tw-py-3 tw-rounded-xl tw-bg-slate-100 tw-text-slate-700 tw-font-semibold hover:tw-bg-slate-200 tw-transition-colors tw-w-full sm:tw-w-auto">
                        Kembali
                    </button>
                    <button type="button" id="btnSaveDraft" class="tw-border-0 tw-px-6 tw-py-3 tw-rounded-xl tw-bg-amber-100 tw-text-amber-700 tw-font-semibold hover:tw-bg-amber-200 tw-transition-colors tw-w-full sm:tw-w-auto">
                        Simpan Sementara
                    </button>
                </div>

                <div class="tw-flex tw-flex-col sm:tw-flex-row tw-gap-3 tw-w-full md:tw-w-auto">
                    <button type="button" id="btnNext" class="tw-border-0 tw-px-8 tw-py-3 tw-rounded-xl tw-bg-primary tw-text-white tw-font-semibold hover:tw-bg-primary-dark tw-shadow-lg tw-shadow-primary/30 tw-transition-all tw-w-full sm:tw-w-auto">
                        Selanjutnya
                    </button>
                    <button type="submit" id="btnSubmit" class="tw-hidden tw-border-0 tw-px-8 tw-py-3 tw-rounded-xl tw-bg-emerald-500 tw-text-white tw-font-semibold hover:tw-bg-emerald-600 tw-shadow-lg tw-shadow-emerald-500/30 tw-transition-all tw-w-full sm:tw-w-auto disabled:tw-opacity-50 disabled:tw-cursor-not-allowed">
                        Submit Penilaian
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalCategories = {{ count($categories) }};
    const totalQuestions = document.querySelectorAll('.form-score-input').length;
    let currentTabIndex = 0;
    
    const tabs = document.querySelectorAll('.category-tab');
    const tabContents = document.querySelectorAll('.tab-content:not(#tab-notes)');
    const btnNext = document.getElementById('btnNext');
    const btnPrev = document.getElementById('btnPrev');
    const btnSubmit = document.getElementById('btnSubmit');
    const categoryTitle = document.getElementById('currentCategoryTitle');
    const categoryIndexText = document.getElementById('currentCategoryIndex');
    
    // Switch Tab Logic
    function switchTab(index) {
        if(index < 0 || index >= totalCategories) return;
        
        // Hide all
        tabContents.forEach(c => c.classList.remove('active'));
        tabs.forEach(t => {
            t.classList.remove('tw-bg-primary', 'tw-text-white', 'tw-shadow-md', 'tw-bg-transparent');
            t.classList.add('tw-text-slate-600', 'tw-bg-transparent');
            t.querySelector('.tab-badge').classList.remove('tw-bg-white/20');
            t.querySelector('.tab-badge').classList.add('tw-bg-slate-200');
        });
        
        // Show current
        document.getElementById('tab-' + index).classList.add('active');
        tabs[index].classList.remove('tw-text-slate-600', 'tw-bg-transparent');
        tabs[index].classList.add('tw-bg-primary', 'tw-text-white', 'tw-shadow-md');
        tabs[index].querySelector('.tab-badge').classList.remove('tw-bg-slate-200');
        tabs[index].querySelector('.tab-badge').classList.add('tw-bg-white/20');
        
        // Update Title
        let titleText = tabs[index].textContent.replace(/[0-9]+\/[0-9]+/, '').trim();
        categoryTitle.textContent = titleText;
        categoryIndexText.textContent = index + 1;
        
        currentTabIndex = index;
        
        // Buttons logic
        if (index === 0) {
            btnPrev.classList.add('tw-hidden');
        } else {
            btnPrev.classList.remove('tw-hidden');
        }
        
        if (index === totalCategories - 1) {
            btnNext.classList.add('tw-hidden');
            btnSubmit.classList.remove('tw-hidden');
        } else {
            btnNext.classList.remove('tw-hidden');
            btnSubmit.classList.add('tw-hidden');
            document.getElementById('tab-notes').classList.remove('active');
        }
        
        tabs[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Tab Clicks
    tabs.forEach((tab, idx) => {
        tab.addEventListener('click', () => switchTab(idx));
    });

    // Next/Prev Buttons
    btnNext.addEventListener('click', () => switchTab(currentTabIndex + 1));
    btnPrev.addEventListener('click', () => switchTab(currentTabIndex - 1));

    // Scoring Component Logic
    const inputs = document.querySelectorAll('.form-score-input');
    const numbers = document.querySelectorAll('.rating-number');
    
    function updateProgress() {
        let answered = 0;
        const categoryCounts = new Array(totalCategories).fill(0);
        
        inputs.forEach(input => {
            if (input.dataset.answered === "true") {
                answered++;
                categoryCounts[parseInt(input.dataset.category)]++;
            }
        });
        
        // Update progress bar
        const percentage = Math.round((answered / totalQuestions) * 100);
        document.getElementById('progressBar').style.width = percentage + '%';
        document.getElementById('progressText').textContent = percentage + '%';
        
        // Update badges
        tabs.forEach((tab, idx) => {
            const totalInCategory = document.querySelectorAll('#tab-' + idx + ' .form-score-input').length;
            const badge = document.getElementById('badge-' + idx);
            badge.textContent = categoryCounts[idx] + '/' + totalInCategory;
        });
        
        // Enable submit button
        if (answered === totalQuestions) {
            btnSubmit.removeAttribute('disabled');
        } else {
            btnSubmit.setAttribute('disabled', 'true');
        }

        // Show/hide comment box based on last category answers
        const totalInLastCategory = document.querySelectorAll('#tab-' + (totalCategories - 1) + ' .form-score-input').length;
        const lastCategoryFinished = (categoryCounts[totalCategories - 1] === totalInLastCategory);
        
        if (currentTabIndex === totalCategories - 1 && lastCategoryFinished) {
            document.getElementById('tab-notes').classList.add('active');
        } else {
            document.getElementById('tab-notes').classList.remove('active');
        }
    }

    function revertSaveDraftButton() {
        const btnSaveDraft = document.getElementById('btnSaveDraft');
        if (btnSaveDraft && btnSaveDraft.classList.contains('tw-bg-green-100')) {
            btnSaveDraft.textContent = 'Simpan Sementara';
            btnSaveDraft.classList.remove('tw-bg-green-100', 'tw-text-green-700');
            btnSaveDraft.classList.add('tw-bg-amber-100', 'tw-text-amber-700');
        }
    }

    function syncUI(inputId, val) {
        const input = document.getElementById(inputId);
        input.value = val;
        input.dataset.answered = "true";
        
        // Update active number
        document.querySelectorAll(`[data-input="${inputId}"]`).forEach(btn => {
            if (btn.dataset.value === val) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        // Update track progress
        const track = document.getElementById('track_' + inputId.replace('score_', ''));
        if (track) {
            track.style.display = 'block';
            track.style.width = ((val - 1) * 11.11) + '%'; // Adjust width scale since it starts from 1 (1-10 scale)
        }
        
        revertSaveDraftButton();
        updateProgress();
    }

    // Number Click
    numbers.forEach(btn => {
        btn.addEventListener('click', function() {
            const inputId = this.dataset.input;
            const val = this.dataset.value;
            syncUI(inputId, val);
        });
    });

    // Slider Drag
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            syncUI(this.id, this.value);
        });
    });

    // Save Draft Dummy
    document.getElementById('btnSaveDraft').addEventListener('click', function() {
        const data = {};
        inputs.forEach(i => {
            data[i.id] = {
                val: i.value,
                answered: i.dataset.answered
            };
        });
        data['general_notes'] = document.querySelector('textarea[name="general_notes"]').value;
        localStorage.setItem('draft_assessment_{{ $target->id }}', JSON.stringify(data));
        
        this.innerHTML = 'Tersimpan Lokal';
        this.classList.remove('tw-bg-amber-100', 'tw-text-amber-700');
        this.classList.add('tw-bg-green-100', 'tw-text-green-700');
    });

    // Textarea input listener to revert button
    const notesTextarea = document.querySelector('textarea[name="general_notes"]');
    if (notesTextarea) {
        notesTextarea.addEventListener('input', revertSaveDraftButton);
    }

    // Load Draft
    const draft = localStorage.getItem('draft_assessment_{{ $target->id }}');
    if (draft) {
        const data = JSON.parse(draft);
        for (const [id, item] of Object.entries(data)) {
            if (id === 'general_notes') {
                document.querySelector('textarea[name="general_notes"]').value = item;
            } else if (item && typeof item === 'object') {
                if (item.answered === "true") {
                    syncUI(id, item.val);
                }
            } else {
                if (item !== "") syncUI(id, item);
            }
        }
    }
    
    // Clear draft on submit
    document.getElementById('assessmentForm').addEventListener('submit', function() {
        if (!confirm('Apakah Anda yakin jawaban kuesioner ini sudah sesuai? Penilaian yang diserahkan tidak dapat diubah.')) {
            event.preventDefault();
            return false;
        }
        localStorage.removeItem('draft_assessment_{{ $target->id }}');
    });
    
    // Initialize Progress
    updateProgress();
});
</script>
@endpush
