<div class="breadcrumb-wrapper bg-cover" style="background-image: url('/assets/img/breadcrumb.png');">
    <div class="container">
        <div class="page-heading">
            <div class="breadcrumb-sub-title">
                <h1 class="wow fadeInUp" data-wow-delay=".3s">{{ $title }}</h1>
            </div>
            <ul class="breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
                @foreach ($links as $link)
                    <li>
                        @if (isset($link['url']))
                            <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
                        @else
                            {{ $link['label'] }}
                        @endif
                    </li>
                    @if (!$loop->last)
                        <li>
                            <i class="fa-solid fa-chevron-right"></i>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>
