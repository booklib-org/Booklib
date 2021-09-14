<div class="c-sidebar-brand d-lg-down-none">
    BookLib
</div>

@if(\App\Models\Job::where("payload", "LIKE", "%RescanLibrary%")->exists())
<div class="c-sidebar-brand d-lg-down-none">
    Your library is currently updating
</div>
     @endif
