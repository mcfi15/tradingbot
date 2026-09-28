@extends('templates.york.blades.layouts.user')

@section('content')
<div class="min-h-screen relative">
    <div class="fixed top-0 right-0 w-[40rem] h-[40rem] bg-accent-primary/5 rounded-full blur-[120px] pointer-events-none -z-0 -translate-y-1/3 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-0 w-[30rem] h-[30rem] bg-teal-500/5 rounded-full blur-[120px] pointer-events-none -z-0 translate-y-1/3 -translate-x-1/3"></div>
    <div class="relative z-10 max-w-6xl mx-auto">

        {{-- FORM STATE --}}
        <div id="kyc-submission-form" class="{{ isset($last_kyc) && $last_kyc ? 'hidden' : '' }}">
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-accent-primary animate-pulse shadow-[0_0_8px_rgba(226,177,60,0.7)]"></span>
                    <span class="text-[9px] font-bold uppercase tracking-[0.25em] text-slate-500">{{ __('Identity Verification') }}</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-slate-500 tracking-tight leading-tight">{{ __('Verify Your Identity') }}</h1>
                <p class="text-slate-500 text-sm mt-2 font-medium">{{ __('Complete the steps below to verify your account and unlock all features.') }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                {{-- Left Rail --}}
                <div class="lg:col-span-4 lg:sticky lg:top-24">
                    <div class="flex items-center gap-3 mb-6 p-4 rounded-2xl border border-white/5 bg-white/[0.02]">
                        <div class="w-10 h-10 rounded-full bg-accent-primary/10 border border-accent-primary/20 flex items-center justify-center text-accent-primary font-black text-sm shrink-0">
                            {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-white font-bold text-sm truncate">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                            <div class="text-[9px] text-slate-500 uppercase tracking-widest font-bold mt-0.5">{{ __('Not Verified') }}</div>
                        </div>
                        <div class="ml-auto shrink-0">
                            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-60"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-amber-400"></span></span>
                        </div>
                    </div>

                    <div class="relative pl-2" id="step-rail">
                        @php
                            $kycSteps = [1=>__('Personal Details'),2=>__('Document Upload'),3=>__('Selfie Verification'),4=>__('Address Verification'),5=>__('Security Check'),6=>__('Review & Submit')];
                        @endphp
                        @foreach ($kycSteps as $num => $label)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div id="rail-node-{{ $num }}" class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 border transition-all duration-500 z-10 {{ $num===1?'bg-accent-primary/15 border-accent-primary/50 text-accent-primary shadow-[0_0_16px_rgba(226,177,60,0.25)]':'bg-white/[0.03] border-white/[0.08] text-slate-600' }}">
                                    <svg id="rail-check-{{ $num }}" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span id="rail-num-{{ $num }}" class="text-[10px] font-black">{{ $num }}</span>
                                </div>
                                @if (!$loop->last)
                                <div id="rail-line-{{ $num }}" class="w-px flex-1 my-1 min-h-[2rem] transition-all duration-700" style="background:rgba(255,255,255,0.06)"></div>
                                @endif
                            </div>
                            <div class="pb-6 pt-1.5 {{ $loop->last?'pb-0':'' }}">
                                <span id="rail-label-{{ $num }}" class="text-xs font-bold uppercase tracking-wider transition-colors duration-300 {{ $num===1?'text-white':'text-slate-600' }}">{{ $label }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6 p-4 rounded-xl border border-white/5 bg-white/[0.02] flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <p class="text-[10px] text-slate-500 leading-relaxed font-medium">{{ __('Your data is encrypted end-to-end and never shared with third parties.') }}</p>
                    </div>
                </div>
                {{-- Right Form Panel --}}
                <div class="lg:col-span-8">
                    <form id="kyc-form" method="POST" enctype="multipart/form-data" class="relative rounded-2xl overflow-hidden" style="background:linear-gradient(145deg,rgba(8,9,14,0.97) 0%,rgba(5,6,10,0.99) 100%);border:1px solid rgba(255,255,255,0.06);">
                        @csrf
                        <div class="absolute top-0 right-0 w-64 h-64 bg-accent-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                        <div class="absolute bottom-0 left-0 w-48 h-48 bg-teal-500/5 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>

                        <div class="p-6 lg:p-8 relative z-10 min-h-[440px] flex flex-col">

                            {{-- STEP 1 --}}
                            <div class="kyc-step flex-1 flex flex-col" data-step="1">
                                <div class="mb-6"><h3 class="text-lg font-black text-white tracking-tight">{{ __('Personal Details') }}</h3><p class="text-[11px] text-slate-500 mt-1">{{ __('Enter your legal name exactly as it appears on your ID.') }}</p></div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('First Name') }}</label>
                                        <input type="text" name="first_name" value="{{ Auth::user()->first_name }}" class="w-full bg-white/[0.03] border border-white/[0.08] rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 focus:ring-1 focus:ring-accent-primary/30 transition-all placeholder:text-slate-600" placeholder="John">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Last Name') }}</label>
                                        <input type="text" name="last_name" value="{{ Auth::user()->last_name }}" class="w-full bg-white/[0.03] border border-white/[0.08] rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 focus:ring-1 focus:ring-accent-primary/30 transition-all placeholder:text-slate-600" placeholder="Doe">
                                    </div>
                                    <div class="space-y-1.5 md:col-span-2">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Date of Birth') }}</label>
                                        <div class="grid grid-cols-3 gap-3">
                                            <select name="dob_day" id="dob_day" class="bg-white/[0.03] border border-white/[0.08] rounded-xl px-3 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 appearance-none text-center cursor-pointer transition-all">
                                                <option value="" disabled selected>{{ __('Day') }}</option>
                                                @for ($i=1;$i<=31;$i++)<option value="{{ $i }}" class="bg-[#0a0b10]">{{ $i }}</option>@endfor
                                            </select>
                                            <select name="dob_month" id="dob_month" class="bg-white/[0.03] border border-white/[0.08] rounded-xl px-3 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 appearance-none text-center cursor-pointer transition-all">
                                                <option value="" disabled selected>{{ __('Month') }}</option>
                                                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $idx=>$mon)
                                                <option value="{{ $idx+1 }}" class="bg-[#0a0b10]">{{ __($mon) }}</option>
                                                @endforeach
                                            </select>
                                            <select name="dob_year" id="dob_year" class="bg-white/[0.03] border border-white/[0.08] rounded-xl px-3 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 appearance-none text-center cursor-pointer transition-all">
                                                <option value="" disabled selected>{{ __('Year') }}</option>
                                                @for($i=date('Y')-18;$i>=date('Y')-100;$i--)<option value="{{ $i }}" class="bg-[#0a0b10]">{{ $i }}</option>@endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5 md:col-span-2">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Phone Number') }}</label>
                                        <div class="flex gap-2">
                                            <div class="relative w-[130px] shrink-0">
                                                <select name="phone_code" id="country_code_select" class="w-full bg-white/[0.03] border border-white/[0.08] rounded-xl pl-9 pr-3 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 appearance-none truncate cursor-pointer transition-all">
                                                    @foreach($countries as $country)
                                                    <option value="{{ $country['dial_code'] }}" data-flag="{{ strtolower($country['code']) }}" class="bg-[#0a0b10]" {{ $country['code']=='US'?'selected':'' }}>{{ $country['code'] }} ({{ $country['dial_code'] }})</option>
                                                    @endforeach
                                                </select>
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><img src="{{ asset('assets/flags/us.svg') }}" class="w-4 h-auto rounded-sm object-cover" id="selected_flag"></div>
                                            </div>
                                            <input type="tel" name="phone" class="flex-1 bg-white/[0.03] border border-white/[0.08] rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 focus:ring-1 focus:ring-accent-primary/30 transition-all placeholder:text-slate-600" placeholder="1234567890" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 2 --}}
                            <div class="kyc-step hidden flex-1 flex flex-col" data-step="2">
                                <div class="mb-6"><h3 class="text-lg font-black text-white tracking-tight">{{ __('Document Upload') }}</h3><p class="text-[11px] text-slate-500 mt-1">{{ __('Select your document type and upload clear, unobstructed photos.') }}</p></div>
                                <div class="mb-6">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-3">{{ __('Document Type') }}</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        @foreach([['value'=>'passport','label'=>__('Passport')],['value'=>'id_card','label'=>__('National ID')],['value'=>'drivers_license','label'=>__("Driver's License")]] as $doc)
                                        <label class="cursor-pointer"><input type="radio" name="document_type" value="{{ $doc['value'] }}" class="peer sr-only" {{ $doc['value']==='passport'?'checked':'' }}><div class="py-3 px-2 rounded-xl border border-white/[0.08] bg-white/[0.03] text-slate-500 peer-checked:bg-accent-primary/10 peer-checked:text-accent-primary peer-checked:border-accent-primary/40 transition-all text-center text-[11px] font-bold uppercase tracking-wider">{{ $doc['label'] }}</div></label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">{{ __('Front Side') }}</span>
                                        <div class="relative group"><input type="file" name="doc_front" id="doc_front" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*"><div class="h-40 rounded-xl border-2 border-dashed border-white/[0.08] bg-white/[0.02] flex flex-col items-center justify-center transition-all group-hover:border-accent-primary/30 group-hover:bg-accent-primary/5" id="preview_doc_front_container"><svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg><span class="text-[11px] text-slate-500 font-medium">{{ __('Click to upload') }}</span><span class="text-[9px] text-slate-600 mt-0.5">JPG, PNG (Max 5MB)</span></div><img id="preview_doc_front" class="absolute inset-0 w-full h-full object-cover rounded-xl hidden pointer-events-none"></div>
                                    </div>
                                    <div class="space-y-1.5" id="back_side_container">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">{{ __('Back Side') }}</span>
                                        <div class="relative group"><input type="file" name="doc_back" id="doc_back" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*"><div class="h-40 rounded-xl border-2 border-dashed border-white/[0.08] bg-white/[0.02] flex flex-col items-center justify-center transition-all group-hover:border-accent-primary/30 group-hover:bg-accent-primary/5" id="preview_doc_back_container"><svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg><span class="text-[11px] text-slate-500 font-medium">{{ __('Click to upload') }}</span><span class="text-[9px] text-slate-600 mt-0.5">JPG, PNG (Max 5MB)</span></div><img id="preview_doc_back" class="absolute inset-0 w-full h-full object-cover rounded-xl hidden pointer-events-none"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 3 --}}
                            <div class="kyc-step hidden flex-1 flex flex-col" data-step="3">
                                <div class="mb-6"><h3 class="text-lg font-black text-white tracking-tight">{{ __('Selfie Verification') }}</h3><p class="text-[11px] text-slate-500 mt-1">{{ __('Hold your ID next to your face so both are clearly visible.') }}</p></div>
                                <input type="file" name="selfie" id="selfie_input" class="hidden" accept="image/*">
                                <div class="flex flex-col items-center justify-center flex-1">
                                    <div class="relative w-full max-w-xs aspect-[3/4] rounded-2xl overflow-hidden border border-white/[0.08] bg-black/40 flex flex-col items-center justify-center" id="camera_container">
                                        <div class="absolute inset-4 pointer-events-none z-20">
                                            <div class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-accent-primary/60 rounded-tl-sm"></div>
                                            <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-accent-primary/60 rounded-tr-sm"></div>
                                            <div class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-accent-primary/60 rounded-bl-sm"></div>
                                            <div class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-accent-primary/60 rounded-br-sm"></div>
                                        </div>
                                        <div id="camera_placeholder" class="text-center p-6">
                                            <div class="w-14 h-14 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                                            <p class="text-[11px] text-slate-500 mb-4 leading-relaxed">{{ __('Position your face in the frame and hold your document clearly visible') }}</p>
                                            <button type="button" onclick="startCamera()" class="px-5 py-2.5 bg-accent-primary/10 hover:bg-accent-primary/20 border border-accent-primary/30 text-accent-primary rounded-xl font-bold text-[11px] transition-all cursor-pointer">{{ __('Launch Camera') }}</button>
                                        </div>
                                        <video id="camera_video" class="absolute inset-0 w-full h-full object-cover hidden" autoplay playsinline muted></video>
                                        <canvas id="camera_canvas" class="hidden"></canvas>
                                        <img id="selfie_preview" class="absolute inset-0 w-full h-full object-cover hidden pointer-events-none">
                                        <div id="camera_ui" class="absolute bottom-4 left-0 right-0 flex justify-center hidden z-30"><button type="button" onclick="capturePhoto()" class="w-14 h-14 rounded-full border-4 border-white/80 bg-white/20 hover:bg-white/40 transition-all flex items-center justify-center cursor-pointer"><div class="w-10 h-10 bg-white rounded-full"></div></button></div>
                                        <div id="retake_ui" class="absolute bottom-4 left-0 right-0 flex justify-center hidden z-30"><button type="button" onclick="retakePhoto()" class="px-5 py-2 bg-black/60 hover:bg-black/80 text-white rounded-full font-bold text-[11px] border border-white/20 transition-colors cursor-pointer">{{ __('Retake') }}</button></div>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 4 --}}
                            <div class="kyc-step hidden flex-1 flex flex-col" data-step="4">
                                <div class="mb-6"><h3 class="text-lg font-black text-white tracking-tight">{{ __('Address Verification') }}</h3><p class="text-[11px] text-slate-500 mt-1">{{ __('Enter your current residential address and upload a recent proof of address document.') }}</p></div>
                                <div class="space-y-4">
                                    <div class="space-y-1.5"><label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Address Line 1') }}</label><input type="text" name="address_line_1" class="w-full bg-white/[0.03] border border-white/[0.08] rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 focus:ring-1 focus:ring-accent-primary/30 transition-all placeholder:text-slate-600" placeholder="123 Main St"></div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1.5"><label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('City') }}</label><input type="text" name="city" class="w-full bg-white/[0.03] border border-white/[0.08] rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 focus:ring-1 focus:ring-accent-primary/30 transition-all placeholder:text-slate-600" placeholder="New York"></div>
                                        <div class="space-y-1.5"><label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('ZIP') }}</label><input type="text" name="zip" class="w-full bg-white/[0.03] border border-white/[0.08] rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 focus:ring-1 focus:ring-accent-primary/30 transition-all placeholder:text-slate-600" placeholder="10001"></div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Country') }}</label>
                                        <div class="relative">
                                            <select name="country" id="address_country" class="w-full bg-white/[0.03] border border-white/[0.08] rounded-xl pl-10 pr-4 py-3 text-white text-sm focus:outline-none focus:border-accent-primary/60 appearance-none cursor-pointer transition-all" onchange="updateAddressFlag(this)">
                                                <option value="" class="bg-[#0a0b10]">{{ __('Select Country') }}</option>
                                                @foreach($countries as $country)<option value="{{ $country['code'] }}" data-flag="{{ strtolower($country['code']) }}" class="bg-[#0a0b10]">{{ $country['name'] }}</option>@endforeach
                                            </select>
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><img src="{{ asset('assets/flags/us.svg') }}" class="w-5 h-auto rounded-sm object-cover hidden" id="address_flag_img"><div id="address_flag_placeholder" class="w-5 h-3 bg-white/10 rounded-sm"></div></div>
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">{{ __('Proof of Address') }}</label>
                                        <p class="text-[10px] text-slate-600">{{ __('Utility bill, bank statement, or tax document — dated within the last 3 months.') }}</p>
                                        <div class="relative group">
                                            <input type="file" name="proof_address" id="proof_address" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*,.pdf">
                                            <div class="h-20 rounded-xl border-2 border-dashed border-white/[0.08] bg-white/[0.02] flex items-center justify-center gap-3 transition-all group-hover:border-accent-primary/30 group-hover:bg-accent-primary/5" id="preview_proof_address_container">
                                                <div class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                                                <div><span class="block text-sm font-bold text-white">{{ __('Upload Document') }}</span><span class="block text-[10px] text-slate-500">{{ __('PDF, JPG, or PNG (Max 5MB)') }}</span></div>
                                            </div>
                                            <div id="preview_proof_address_name" class="absolute inset-0 bg-[#0a0b10]/90 rounded-xl flex items-center justify-center hidden pointer-events-none border border-accent-primary/40"><span class="text-accent-primary font-bold text-sm flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span class="filename truncate max-w-[200px]"></span></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 5 --}}
                            <div class="kyc-step hidden flex-1 flex flex-col" data-step="5">
                                <div class="mb-6"><h3 class="text-lg font-black text-white tracking-tight">{{ __('Security Check') }}</h3><p class="text-[11px] text-slate-500 mt-1">{{ __('A standard security check required to protect your account.') }}</p></div>
                                <div class="rounded-xl p-5 mb-6 flex items-start gap-4" style="background:rgba(226,177,60,0.05);border:1px solid rgba(226,177,60,0.12);">
                                    <div class="w-9 h-9 rounded-full bg-accent-primary/15 flex items-center justify-center shrink-0"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-accent-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                                    <div><h4 class="font-bold text-white text-sm mb-1">{{ __('Security Verification') }}</h4><p class="text-slate-500 text-xs leading-relaxed">{{ __('By proceeding, you agree to our automated security checks. Your personal information is kept confidential and processed securely.') }}</p></div>
                                </div>
                                <div class="mb-6">
                                    <input type="checkbox" id="aml_consent" name="aml_consent" class="hidden" onchange="toggleAmlCheckbox(this)">
                                    <label for="aml_consent" id="aml_label" class="flex items-start gap-4 p-5 rounded-xl bg-white/[0.03] border border-white/[0.08] cursor-pointer transition-all hover:bg-white/[0.06] group">
                                        <div id="aml_checkbox_box" class="w-5 h-5 rounded-md border-2 border-white/20 bg-black/30 flex items-center justify-center transition-all shrink-0 mt-0.5"><svg id="aml_checkmark" class="w-3 h-3 opacity-0 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                                        <div class="flex-1"><span id="aml_title" class="block text-sm font-bold text-white group-hover:text-accent-primary transition-colors">{{ __('I Agree to Security Checks') }}</span><span class="block text-xs text-slate-500 mt-1">{{ __('I confirm that the information provided is accurate and agree to the verification process.') }}</span></div>
                                    </label>
                                </div>
                                <div id="aml-loading" class="hidden text-center py-8">
                                    <div class="relative w-12 h-12 mx-auto mb-4"><div class="absolute inset-0 rounded-full border-4 border-white/5"></div><div class="absolute inset-0 rounded-full border-4 border-t-accent-primary animate-spin"></div></div>
                                    <p class="text-sm font-bold text-white">{{ __('Checking details...') }}</p><p class="text-xs text-slate-500 mt-1">{{ __('This usually takes less than a minute.') }}</p>
                                </div>
                            </div>

                            {{-- STEP 6 --}}
                            <div class="kyc-step hidden flex-1 flex flex-col" data-step="6">
                                <div class="mb-6"><h3 class="text-lg font-black text-white tracking-tight">{{ __('Review & Submit') }}</h3><p class="text-[11px] text-slate-500 mt-1">{{ __('Verify all information is correct before submitting your application.') }}</p></div>
                                <div class="space-y-3">
                                    <div class="rounded-xl p-4 border border-white/[0.08] bg-white/[0.02]">
                                        <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>{{ __('Personal Information') }}</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <div><span class="text-[9px] text-slate-600 uppercase tracking-wider block mb-1">{{ __('Full Name') }}</span><span class="font-bold text-white text-sm" id="review_fullname">--</span></div>
                                            <div><span class="text-[9px] text-slate-600 uppercase tracking-wider block mb-1">{{ __('Date of Birth') }}</span><span class="font-bold text-white text-sm" id="review_dob">--</span></div>
                                            <div><span class="text-[9px] text-slate-600 uppercase tracking-wider block mb-1">{{ __('Phone') }}</span><span class="font-bold text-white text-sm" id="review_phone">--</span></div>
                                        </div>
                                    </div>
                                    <div class="rounded-xl p-4 border border-white/[0.08] bg-white/[0.02]">
                                        <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>{{ __('Residential Address') }}</h4>
                                        <p class="text-white text-sm leading-relaxed" id="review_address">--</p>
                                    </div>
                                    <div class="rounded-xl p-4 border border-white/[0.08] bg-white/[0.02]">
                                        <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>{{ __('Documents') }}</h4>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="flex items-center gap-2.5 rounded-lg bg-black/30 px-3 py-2.5 border border-white/5"><div class="w-7 h-7 rounded bg-accent-primary/15 text-accent-primary flex items-center justify-center shrink-0"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div><span class="text-[9px] text-slate-600 block">{{ __('ID Type') }}</span><span class="text-white font-bold text-xs capitalize" id="review_doc_type_label">--</span></div></div>
                                            <div class="flex items-center gap-2.5 rounded-lg bg-black/30 px-3 py-2.5 border border-white/5"><div class="w-7 h-7 rounded bg-emerald-500/15 text-emerald-500 flex items-center justify-center shrink-0"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div><div><span class="text-[9px] text-slate-600 block">{{ __('Security Check') }}</span><span class="text-emerald-400 font-bold text-xs">{{ __('Passed') }}</span></div></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-emerald-400 text-xs justify-center bg-emerald-500/5 p-3 rounded-xl border border-emerald-500/10"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span class="font-bold">{{ __('Security Check Complete — Ready to Submit') }}</span></div>
                                </div>
                            </div>

                            {{-- SUCCESS --}}
                            <div class="kyc-step hidden flex-1 flex flex-col items-center justify-center text-center py-12" data-step="success">
                                <div class="relative mb-6"><div class="w-20 h-20 rounded-full bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20"><svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div><div class="absolute inset-0 rounded-full bg-emerald-500/5 animate-ping"></div></div>
                                <h3 class="text-2xl font-black text-white mb-2">{{ __('Verification Submitted') }}</h3>
                                <p class="text-slate-500 max-w-sm mx-auto mb-8 text-sm leading-relaxed">{{ __('Your documents are being reviewed. Most verifications are completed within a few minutes, though some may take up to 24 hours.') }}</p>
                                <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2.5 px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 text-white rounded-xl font-bold text-sm transition-all"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>{{ __('Return to Dashboard') }}</a>
                            </div>

                        </div>

                        {{-- Nav footer --}}
                        <div class="px-6 lg:px-8 py-4 border-t border-white/5 flex justify-between items-center" id="form-actions">
                            <button type="button" id="btn-back" class="hidden items-center gap-2 px-5 py-2.5 rounded-xl border border-white/[0.08] text-slate-400 font-bold text-sm hover:text-white hover:bg-white/5 transition-all cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>{{ __('Back') }}</button>
                            <div class="ml-auto flex items-center gap-3">
                                <button type="button" id="btn-next" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-accent-primary/90 hover:bg-accent-primary text-black font-black text-sm transition-all shadow-lg shadow-accent-primary/20 cursor-pointer">{{ __('Continue') }}<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></button>
                                <button type="submit" id="btn-submit" class="hidden items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-500/90 hover:bg-emerald-500 text-white font-black text-sm transition-all shadow-lg shadow-emerald-500/20 cursor-pointer">{{ __('Submit Verification') }}<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></button>
                            </div>
                        </div>
</form>
                </div>
            </div>
        </div>

        {{-- STATUS STATE --}}
        @if(isset($last_kyc)&&$last_kyc)
        @php
            $st=$last_kyc->status; $isApproved=$st==='approved'; $isRejected=$st==='rejected'; $isPending=!$isApproved&&!$isRejected;
            $statusLabel=$isApproved?__('Verified'):($isRejected?__('Action Required'):__('Under Review'));
            $statusClr=$isApproved?'emerald':($isRejected?'rose':'amber');
            $isReviewComplete=$isApproved||$isRejected;
        @endphp
        <div id="kyc-status-card">
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-3"><span class="w-1.5 h-1.5 rounded-full bg-{{ $statusClr }}-400 animate-pulse"></span><span class="text-[9px] font-bold uppercase tracking-[0.25em] text-slate-500">{{ __('Identity Verification') }}</span></div>
                <h1 class="text-3xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-slate-500 tracking-tight leading-tight">{{ __('Verification Status') }}</h1>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-5">
                    <div class="relative rounded-2xl overflow-hidden p-6" style="background:linear-gradient(145deg,rgba(8,9,14,0.97) 0%,rgba(5,6,10,0.99) 100%);border:1px solid rgba(255,255,255,0.06);">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-accent-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('ID Application') }}</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $isApproved?'bg-emerald-500/10 border-emerald-500/20 text-emerald-400':($isRejected?'bg-rose-500/10 border-rose-500/20 text-rose-400':'bg-amber-400/10 border-amber-400/20 text-amber-400') }}"><span class="w-1.5 h-1.5 rounded-full {{ $isApproved?'bg-emerald-400':($isRejected?'bg-rose-400':'bg-amber-400 animate-pulse') }}"></span>{{ $statusLabel }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                            <div class="bg-white/[0.02] rounded-lg p-3 border border-white/5"><div class="text-slate-600 mb-1">{{ __('Full Name') }}</div><div class="text-white font-bold">{{ $last_kyc->user->first_name }} {{ $last_kyc->user->last_name }}</div></div>
                            <div class="bg-white/[0.02] rounded-lg p-3 border border-white/5"><div class="text-slate-600 mb-1">{{ __('Date of Birth') }}</div><div class="text-white font-bold">{{ \Carbon\Carbon::parse($last_kyc->date_of_birth)->format('d M Y') }}</div></div>
                            <div class="bg-white/[0.02] rounded-lg p-3 border border-white/5"><div class="text-slate-600 mb-1">{{ __('Phone') }}</div><div class="text-white font-bold">+{{ $last_kyc->phone_code }} {{ $last_kyc->phone }}</div></div>
                            <div class="bg-white/[0.02] rounded-lg p-3 border border-white/5"><div class="text-slate-600 mb-1">{{ __('Document') }}</div><div class="text-white font-bold capitalize">{{ __(str_replace('_',' ',$last_kyc->document_type)) }}</div></div>
                            <div class="bg-white/[0.02] rounded-lg p-3 border border-white/5 col-span-2"><div class="text-slate-600 mb-1">{{ __('Address') }}</div><div class="text-white font-bold">{{ $last_kyc->address_line_1 }}, {{ $last_kyc->city }}, {{ $last_kyc->zip }}, {{ $last_kyc->country }}</div></div>
                        </div>
                        <div class="text-[9px] text-slate-600 text-right">{{ __('Submitted') }} {{ $last_kyc->created_at->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="lg:col-span-7">
                    <div class="relative rounded-2xl overflow-hidden p-6 h-full" style="background:linear-gradient(145deg,rgba(8,9,14,0.97) 0%,rgba(5,6,10,0.99) 100%);border:1px solid rgba(255,255,255,0.06);">
                        <h2 class="text-base font-black text-white mb-6">{{ __('Verification Tracker') }}</h2>
                        <div class="space-y-0">
                            <div class="flex gap-4"><div class="flex flex-col items-center"><div class="w-8 h-8 rounded-full bg-emerald-500/15 text-emerald-400 flex items-center justify-center border border-emerald-500/40 shrink-0"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div><div class="w-px flex-1 my-2 bg-emerald-500/20 min-h-[2.5rem]"></div></div><div class="pb-6"><h3 class="text-white font-bold text-sm">{{ __('Submission Received') }}</h3><p class="text-slate-500 text-xs mt-1">{{ __('Documents received on') }} {{ $last_kyc->created_at->format('d M Y') }}</p></div></div>
                            <div class="flex gap-4"><div class="flex flex-col items-center"><div class="w-8 h-8 rounded-full flex items-center justify-center border shrink-0 {{ $isReviewComplete?'bg-emerald-500/15 text-emerald-400 border-emerald-500/40':'bg-accent-primary/15 text-accent-primary border-accent-primary/40' }}">@if($isReviewComplete)<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>@else<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>@endif</div><div class="w-px flex-1 my-2 {{ $isReviewComplete?'bg-emerald-500/20':'bg-white/5' }} min-h-[5rem]"></div></div><div class="pb-6 w-full"><h3 class="{{ $isReviewComplete?'text-white':'text-accent-primary' }} font-bold text-sm">{{ __('Account Review') }}</h3><p class="text-slate-500 text-xs mt-1">{{ $isReviewComplete?__('Review completed successfully.'):__('Our team is currently reviewing your documents.') }}</p><div class="mt-3 space-y-1.5">@foreach(['Identity Document Validity','Proof of Address Verification','Facial Biometric Match','Sanctions List Screening','Risk Assessment Score','Security & Background Checks'] as $chk)<div class="flex items-center gap-2.5 text-xs {{ $isReviewComplete?'text-white':'text-slate-600' }}">@if($isReviewComplete)<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>@else<div class="w-3.5 h-3.5 rounded-full border border-white/10 shrink-0"></div>@endif<span>{{ __($chk) }}</span></div>@endforeach</div></div></div>
                            <div class="flex gap-4"><div class="flex flex-col items-center">@if($isApproved)<div class="w-8 h-8 rounded-full bg-emerald-500/15 text-emerald-400 flex items-center justify-center border border-emerald-500/40 shrink-0"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>@elseif($isRejected)<div class="w-8 h-8 rounded-full bg-rose-500/15 text-rose-400 flex items-center justify-center border border-rose-500/40 shrink-0"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div>@else<div class="w-8 h-8 rounded-full bg-white/[0.03] text-slate-600 flex items-center justify-center border border-white/[0.08] shrink-0"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>@endif</div><div class="pb-2"><h3 class="{{ $isPending?'text-slate-500':'text-white' }} font-bold text-sm">{{ __('Final Decision') }}</h3>@if($isApproved)<span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>{{ __('Approved') }}</span><p class="text-slate-500 text-xs mt-2 leading-relaxed">{{ __('Congratulations! Your identity has been verified. You now have full access to all platform features.') }}</p><a href="{{ route('user.dashboard') }}" class="mt-3 inline-flex items-center gap-2 text-xs font-black text-black bg-accent-primary hover:bg-accent-primary/80 px-4 py-2 rounded-lg transition-colors">{{ __('Go to Dashboard') }}<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>@elseif($isRejected)<span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-400 border border-rose-500/20"><span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>{{ __('Rejected') }}</span>@if($last_kyc->rejection_reason)<div class="mt-3 bg-rose-500/5 border border-rose-500/10 rounded-xl p-3"><span class="text-[9px] uppercase tracking-wider text-rose-500/60 font-bold block mb-1">{{ __('Reason') }}</span><p class="text-rose-300 text-xs">{{ $last_kyc->rejection_reason }}</p></div>@endif<div class="mt-3"><button type="button" onclick="document.getElementById('kyc-submission-form').classList.remove('hidden');document.getElementById('kyc-status-card').classList.add('hidden');" class="inline-flex items-center gap-2 text-xs font-bold text-white bg-white/[0.08] hover:bg-white/15 border border-white/10 px-4 py-2 rounded-lg transition-colors cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 16h5v5"/></svg>{{ __('Try Again') }}</button></div>@else<span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 rounded-full text-[10px] font-black bg-white/5 text-slate-500 border border-white/10"><span class="w-1.5 h-1.5 rounded-full bg-slate-500 animate-pulse"></span>{{ __('Decision Pending') }}</span><p class="text-slate-500 text-xs mt-2 leading-relaxed">{{ __('Our team is currently assessing your application. You will be notified via email once a decision is made.') }}</p>@endif</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){
    let cs=1,ts=6;
    function ur(s){
        for(let i=1;i<=ts;i++){
            var nd=$(`#rail-node-${i}`),lb=$(`#rail-label-${i}`),ck=$(`#rail-check-${i}`),nm=$(`#rail-num-${i}`),ln=$(`#rail-line-${i}`);
            if(i<s){nd.attr('class','w-8 h-8 rounded-full flex items-center justify-center shrink-0 border transition-all duration-500 z-10 bg-emerald-500/15 border-emerald-500/50 text-emerald-400');ck.removeClass('hidden');nm.addClass('hidden');lb.attr('class','text-xs font-bold uppercase tracking-wider transition-colors duration-300 text-emerald-400');if(ln.length)ln.css('background','rgba(52,211,153,0.25)');}
            else if(i===s){nd.attr('class','w-8 h-8 rounded-full flex items-center justify-center shrink-0 border transition-all duration-500 z-10 bg-accent-primary/15 border-accent-primary/50 text-accent-primary shadow-[0_0_16px_rgba(226,177,60,0.25)]');ck.addClass('hidden');nm.removeClass('hidden');lb.attr('class','text-xs font-bold uppercase tracking-wider transition-colors duration-300 text-white');if(ln.length)ln.css('background','rgba(255,255,255,0.06)');}
            else{nd.attr('class','w-8 h-8 rounded-full flex items-center justify-center shrink-0 border transition-all duration-500 z-10 bg-white/[0.03] border-white/[0.08] text-slate-600');ck.addClass('hidden');nm.removeClass('hidden');lb.attr('class','text-xs font-bold uppercase tracking-wider transition-colors duration-300 text-slate-600');if(ln.length)ln.css('background','rgba(255,255,255,0.06)');}
        }
    }
    function ui(){
        $('.kyc-step').addClass('hidden');
        if(cs==='success'){$(`.kyc-step[data-step="success"]`).removeClass('hidden');$('#form-actions').addClass('hidden');return;}
        $(`.kyc-step[data-step="${cs}"]`).removeClass('hidden');
        if(cs===1){$('#btn-back').addClass('hidden').removeClass('inline-flex');}else{$('#btn-back').removeClass('hidden').addClass('inline-flex');}
        if(cs===ts){$('#btn-next').addClass('hidden');$('#btn-submit').removeClass('hidden').addClass('inline-flex');}else{$('#btn-next').removeClass('hidden');$('#btn-submit').addClass('hidden').removeClass('inline-flex');}
        ur(cs);
    }
    function hp(inp,img,con){$(inp).change(function(){if(this.files&&this.files[0]){var r=new FileReader();r.onload=function(e){$(img).attr('src',e.target.result).removeClass('hidden');if(con)$(con).addClass('opacity-0');};r.readAsDataURL(this.files[0]);}});}
    hp('#doc_front','#preview_doc_front','#preview_doc_front_container');
    hp('#doc_back','#preview_doc_back','#preview_doc_back_container');
    $('#proof_address').change(function(){if(this.files&&this.files[0])$('#preview_proof_address_name').removeClass('hidden').find('.filename').text(this.files[0].name);});
    $('#country_code_select').change(function(){$('#selected_flag').attr('src',`{{ asset('assets/flags/') }}/${$(this).find('option:selected').data('flag')}.svg`);});
    function ud(){var m=$('#dob_month').val(),y=$('#dob_year').val(),ds=$('#dob_day'),c=ds.val();if(m&&y){var mx=new Date(y,m,0).getDate();ds.find('option').each(function(){var d=parseInt($(this).val());$(this).prop('disabled',d>mx).toggleClass('hidden',d>mx);});if(c>mx)ds.val('');}}
    $('#dob_month,#dob_year').change(ud);
    window.updateAddressFlag=function(s){var f=$(s).find('option:selected').data('flag');if(f){$('#address_flag_img').attr('src',`{{ asset('assets/flags/') }}/${f}.svg`).removeClass('hidden');$('#address_flag_placeholder').addClass('hidden');}else{$('#address_flag_img').addClass('hidden');$('#address_flag_placeholder').removeClass('hidden');}};
    $('input[name="document_type"]').change(function(){$('#back_side_container').toggleClass('hidden',$(this).val()==='passport');});
    $('input[name="document_type"]:checked').trigger('change');
    let stream=null;
    window.startCamera=async function(){try{stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'user'},audio:false});var v=document.getElementById('camera_video');v.srcObject=stream;v.classList.remove('hidden');$('#camera_placeholder').addClass('hidden');$('#camera_ui').removeClass('hidden');}catch(e){window.toastNotification("{{ __('Could not access camera. Please allow permissions.') }}",'error');}};
    window.capturePhoto=async function(){var v=document.getElementById('camera_video'),c=document.getElementById('camera_canvas');c.width=v.videoWidth;c.height=v.videoHeight;c.getContext('2d').drawImage(v,0,0);if(stream)stream.getTracks().forEach(t=>t.stop());var u=c.toDataURL('image/png');$('#selfie_preview').attr('src',u).removeClass('hidden');$(v).addClass('hidden');$('#camera_ui').addClass('hidden');$('#retake_ui').removeClass('hidden');try{var blob=await(await fetch(u)).blob(),dt=new DataTransfer();dt.items.add(new File([blob],'selfie.png',{type:'image/png'}));document.getElementById('selfie_input').files=dt.files;}catch(e){window.toastNotification('Error saving selfie. Please try again.','error');}};
    window.retakePhoto=function(){$('#selfie_preview').addClass('hidden');$('#retake_ui').addClass('hidden');window.startCamera();};
    window.toggleAmlCheckbox=function(cb){var l=$('#aml_label'),b=$('#aml_checkbox_box'),m=$('#aml_checkmark');if(cb.checked){l.addClass('border-accent-primary/40 bg-accent-primary/5').removeClass('border-white/[0.08] bg-white/[0.03]');b.addClass('bg-accent-primary border-accent-primary').removeClass('border-white/20 bg-black/30');m.removeClass('opacity-0');}else{l.removeClass('border-accent-primary/40 bg-accent-primary/5').addClass('border-white/[0.08] bg-white/[0.03]');b.removeClass('bg-accent-primary border-accent-primary').addClass('border-white/20 bg-black/30');m.addClass('opacity-0');}};
    function vs(s){var ok=true,msg='';switch(s){case 1:if(!$('input[name="first_name"]').val()){ok=false;msg="{{ __('Please enter your first name.') }}";break;}if(!$('input[name="last_name"]').val()){ok=false;msg="{{ __('Please enter your last name.') }}";break;}if(!$('#dob_day').val()||!$('#dob_month').val()||!$('#dob_year').val()){ok=false;msg="{{ __('Please enter your full date of birth.') }}";break;}if(!$('input[name="phone"]').val()){ok=false;msg="{{ __('Please enter your phone number.') }}";break;}break;case 2:if(!$('#doc_front').val()){ok=false;msg="{{ __('Please upload the front side of your document.') }}";break;}if($('input[name="document_type"]:checked').val()!=='passport'&&!$('#doc_back').val()){ok=false;msg="{{ __('Please upload the back side of your document.') }}";break;}break;case 3:if(!$('#selfie_input').get(0).files.length){ok=false;msg="{{ __('Please take a selfie.') }}";break;}break;case 4:if(!$('input[name="address_line_1"]').val()){ok=false;msg="{{ __('Please enter your address.') }}";break;}if(!$('input[name="city"]').val()){ok=false;msg="{{ __('Please enter your city.') }}";break;}if(!$('input[name="zip"]').val()){ok=false;msg="{{ __('Please enter your zip code.') }}";break;}if(!$('select[name="country"]').val()){ok=false;msg="{{ __('Please select your country.') }}";break;}if(!$('#proof_address').val()){ok=false;msg="{{ __('Please upload proof of address.') }}";break;}break;}if(!ok)window.toastNotification(msg,'error');return ok;}
    $('#btn-next').click(function(){if(!vs(cs))return;if(cs===5){if(!$('#aml_consent').is(':checked')){window.toastNotification("{{ __('Please consent to the AML checks to proceed.') }}",'error');return;}$('#btn-next').prop('disabled',true).addClass('opacity-50');$('#aml-loading').removeClass('hidden');setTimeout(function(){$('#btn-next').prop('disabled',false).removeClass('opacity-50');$('#aml-loading').addClass('hidden');$('#review_fullname').text($('input[name="first_name"]').val()+' '+$('input[name="last_name"]').val());$('#review_dob').text(`${$('#dob_day').val()} ${$('#dob_month option:selected').text()} ${$('#dob_year').val()}`);$('#review_phone').text(`+${$('#country_code_select').val()} ${$('input[name="phone"]').val()}`);$('#review_address').text(`${$('input[name="address_line_1"]').val()}, ${$('input[name="city"]').val()}, ${$('input[name="zip"]').val()}, ${$('select[name="country"] option:selected').text()}`);$('#review_doc_type_label').text($('input[name="document_type"]:checked').val().replace(/_/g,' '));cs++;ui();},2000);}else if(cs<ts){cs++;ui();}});
    $('#btn-back').click(function(){if(cs>1){cs--;ui();}});
    $('#kyc-form').submit(function(e){e.preventDefault();$('#btn-submit').prop('disabled',true).addClass('opacity-50 cursor-not-allowed').html('<span class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2 inline-block"></span> {{ __("Submitting...") }}');$.ajax({url:"{{ route('user.kyc.submit') }}",type:'POST',data:new FormData(this),contentType:false,processData:false,headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},success:function(r){cs='success';ui();window.toastNotification(r.message,'success');},error:function(xhr){$('#btn-submit').prop('disabled',false).removeClass('opacity-50 cursor-not-allowed').html(`<span>{{ __('Submit Verification') }}</span><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`);var err=xhr.responseJSON?.errors;window.toastNotification(err?Object.values(err).flat().join('<br>'):(xhr.responseJSON?.message??"{{ __('Something went wrong. Please try again.') }}"),'error');}});});
    ui();
});
</script>
@endsection
