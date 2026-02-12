<div class="col-12">
    <div class="card border-0 shadow-sm hover-shadow">
        <div class="card-header bg-{{ $color }} text-white py-3">
            <h3 class="mb-0 d-flex align-items-center">
                <i class="fas {{ $icon }} me-3 fs-4"></i>
                {{ $title }}
            </h3>
        </div>

        <div class="card-body p-4">

            @if(!empty($videos))
                <div class="row g-4">
                    @foreach($videos as $video)
                        <div class="col-md-6 col-lg-4">
                            <div class="video-card">

                                <div class="video-wrapper rounded overflow-hidden shadow-sm mb-3">
                                    <video class="w-100"
                                           controls
                                           preload="metadata"
                                           controlsList="nodownload">

                                        <source src="{{ $video['url'] }}" type="video/mp4">

                                        Votre navigateur ne supporte pas la lecture de vidéos.
                                    </video>
                                </div>

                                <h5 class="video-title text-dark mb-0">
                                    <i class="fas fa-play-circle text-{{ $color }} me-2"></i>
                                    {{ pathinfo($video['name'], PATHINFO_FILENAME) }}
                                </h5>

                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-video-slash text-muted mb-3" style="font-size: 3rem;"></i>
                    <p class="text-muted mb-0">
                        Aucune vidéo disponible pour ce module.
                    </p>
                </div>
            @endif

        </div>
    </div>
</div>