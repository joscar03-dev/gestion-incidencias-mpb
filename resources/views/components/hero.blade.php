@auth
    <section class="relative overflow-hidden bg-primary pt-20 pb-20 lg:pb-[90px] lg:pt-[120px] min-h-screen flex items-center">
        <!-- Video Carousel Background -->
        <div class="absolute inset-0 z-0" id="videoCarousel">
            <!-- Video 1 -->
            <video
                class="video-slide w-full h-full object-cover absolute inset-0 opacity-100 transition-opacity duration-1000"
                autoplay
                muted
                loop
                playsinline
                data-video="1"
                poster="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkyMCIgaGVpZ2h0PSIxMDgwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMTAwJSIgeTI9IjEwMCUiPjxzdG9wIG9mZnNldD0iMCUiIHN0eWxlPSJzdG9wLWNvbG9yOiMzYjgyZjY7c3RvcC1vcGFjaXR5OjEiIC0+PHN0b3Agb2Zmc2V0PSIxMDAlIiBzdHlsZT0ic3RvcC1jb2xvcjojOGIzNWZmO3N0b3Atb3BhY2l0eToxIiAvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZGllbnQpIiAvPjwvc3ZnPg=="
            >
                <source src="{{ asset('videos/background1.mp4') }}" type="video/mp4">
                <source src="{{ asset('videos/background1.webm') }}" type="video/webm">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-digital-abstract-blue-background-4031-large.mp4" type="video/mp4">
            </video>

            <!-- Video 2 -->
            <video
                class="video-slide w-full h-full object-cover absolute inset-0 opacity-0 transition-opacity duration-1000"
                muted
                loop
                playsinline
                data-video="2"
                poster="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkyMCIgaGVpZ2h0PSIxMDgwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQyIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjojOGIzNWZmO3N0b3Atb3BhY2l0eToxIiAvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3R5bGU9InN0b3AtY29sb3I6IzNiODJmNjtzdG9wLW9wYWNpdHk6MSIgLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWRpZW50MikiIC8+PC9zdmc+"
            >
                <source src="{{ asset('videos/background2.mp4') }}" type="video/mp4">
                <source src="{{ asset('videos/background2.webm') }}" type="video/webm">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-tech-devices-background-in-blue-4033-large.mp4" type="video/mp4">
            </video>

            <!-- Video 3 -->
            <video
                class="video-slide w-full h-full object-cover absolute inset-0 opacity-0 transition-opacity duration-1000"
                muted
                loop
                playsinline
                data-video="3"
                poster="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkyMCIgaGVpZ2h0PSIxMDgwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQzIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjojMDU0OTRmO3N0b3Atb3BhY2l0eToxIiAvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3R5bGU9InN0b3AtY29sb3I6IzA2MmU2ZjtzdG9wLW9wYWNpdHk6MSIgLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWRpZW50MykiIC8+PC9zdmc+"
            >
                <source src="{{ asset('videos/background3.mp4') }}" type="video/mp4">
                <source src="{{ asset('videos/background3.webm') }}" type="video/webm">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-network-mesh-4166-large.mp4" type="video/mp4">
            </video>

            <!-- Video 4 -->
            <video
                class="video-slide w-full h-full object-cover absolute inset-0 opacity-0 transition-opacity duration-1000"
                muted
                loop
                playsinline
                data-video="4"
                poster="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkyMCIgaGVpZ2h0PSIxMDgwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQ0IiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjojNzMxOTZkO3N0b3Atb3BhY2l0eToxIiAvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3R5bGU9InN0b3AtY29sb3I6IzM5MjY4OTtzdG9wLW9wYWNpdHk6MSIgLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWRpZW50NCkiIC8+PC9zdmc+"
            >
                <source src="{{ asset('videos/background4.mp4') }}" type="video/mp4">
                <source src="{{ asset('videos/background4.webm') }}" type="video/webm">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-purple-particles-moving-vertically-26074-large.mp4" type="video/mp4">
            </video>

            <!-- Fallback gradient for when all videos fail -->
            <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-blue-500 via-purple-600 to-indigo-700 animate-gradient-x opacity-0" id="fallbackGradient"></div>

            <!-- Video Carousel Indicators -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                <div class="video-indicator w-2 h-2 rounded-full bg-white/50 transition-all duration-300" data-video="1"></div>
                <div class="video-indicator w-2 h-2 rounded-full bg-white/30 transition-all duration-300" data-video="2"></div>
                <div class="video-indicator w-2 h-2 rounded-full bg-white/30 transition-all duration-300" data-video="3"></div>
                <div class="video-indicator w-2 h-2 rounded-full bg-white/30 transition-all duration-300" data-video="4"></div>
            </div>

            <!-- Video Carousel Controls -->
            <div class="absolute top-1/2 left-4 transform -translate-y-1/2 z-10">
                <button class="video-prev bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full p-2 transition-all duration-300 group" aria-label="Video anterior">
                    <svg class="w-6 h-6 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <div class="absolute top-1/2 right-4 transform -translate-y-1/2 z-10">
                <button class="video-next bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full p-2 transition-all duration-300 group" aria-label="Siguiente video">
                    <svg class="w-6 h-6 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

            <!-- Overlay gradients - TailGrids style -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/40 to-black/60"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/40"></div>

            <!-- Animated particles -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute w-2 h-2 bg-white/20 rounded-full animate-float" style="top: 20%; left: 10%; animation-delay: 0s;"></div>
                <div class="absolute w-3 h-3 bg-blue-400/30 rounded-full animate-float" style="top: 60%; left: 80%; animation-delay: 2s;"></div>
                <div class="absolute w-1 h-1 bg-purple-400/40 rounded-full animate-float" style="top: 40%; left: 60%; animation-delay: 4s;"></div>
                <div class="absolute w-2 h-2 bg-white/15 rounded-full animate-float" style="top: 80%; left: 30%; animation-delay: 6s;"></div>
                <div class="absolute w-1 h-1 bg-blue-300/50 rounded-full animate-float" style="top: 30%; left: 90%; animation-delay: 8s;"></div>
            </div>
        </div>

        <!-- TailGrids decorative elements -->
        <div class="absolute left-1/2 top-0 -z-10 h-full w-full -translate-x-1/2 pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-32 h-32 border border-white/10 rounded-full animate-pulse-slow"></div>
            <div class="absolute top-1/2 right-1/4 w-24 h-24 border border-blue-400/20 rounded-full animate-spin-slow"></div>
            <div class="absolute bottom-1/4 left-1/3 w-40 h-40 border border-purple-400/15 rounded-full animate-pulse-slow" style="animation-delay: 3s;"></div>
        </div>

        <!-- TailGrids dot pattern -->
        <div class="absolute -top-3 left-0 z-[-1] opacity-30">
            <svg width="134" height="106" viewBox="0 0 134 106" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="1.66667" cy="104" r="1.66667" transform="rotate(-90 1.66667 104)" fill="#3B82F6" />
                <circle cx="16.3333" cy="104" r="1.66667" transform="rotate(-90 16.3333 104)" fill="#3B82F6" />
                <circle cx="31" cy="104" r="1.66667" transform="rotate(-90 31 104)" fill="#3B82F6" />
                <circle cx="45.6667" cy="104" r="1.66667" transform="rotate(-90 45.6667 104)" fill="#3B82F6" />
                <circle cx="60.3334" cy="104" r="1.66667" transform="rotate(-90 60.3334 104)" fill="#3B82F6" />
                <circle cx="88.6667" cy="104" r="1.66667" transform="rotate(-90 88.6667 104)" fill="#3B82F6" />
                <circle cx="117.667" cy="104" r="1.66667" transform="rotate(-90 117.667 104)" fill="#3B82F6" />
                <circle cx="74.6667" cy="104" r="1.66667" transform="rotate(-90 74.6667 104)" fill="#3B82F6" />
                <circle cx="103" cy="104" r="1.66667" transform="rotate(-90 103 104)" fill="#3B82F6" />
                <circle cx="132" cy="104" r="1.66667" transform="rotate(-90 132 104)" fill="#3B82F6" />
            </svg>
        </div>

        <div class="container mx-auto relative z-10">
            <div class="-mx-4 flex flex-wrap items-center">
                <div class="w-full px-4 lg:w-7/12">
                    <div class="max-w-[570px] pb-10 text-center lg:text-left">
                        <!-- Badge -->
                        <div class="mb-6 inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500/20 to-purple-500/20 backdrop-blur-sm rounded-full border border-blue-400/30 shadow-lg animate-fade-in-up">
                            <div class="mr-3 relative">
                                <div class="w-3 h-3 bg-blue-400 rounded-full shadow-lg"></div>
                                <div class="absolute inset-0 w-3 h-3 bg-blue-400 rounded-full animate-ping opacity-75"></div>
                            </div>
                            <span class="text-sm font-medium text-white">
                                Sistema de Gestión de Incidencias
                            </span>
                        </div>

                        <!-- Main Title -->
                        <h1 class="mb-6 text-4xl font-bold leading-[1.2] text-white sm:text-5xl lg:text-6xl animate-fade-in-up" style="animation-delay: 0.2s;">
                            Centro de
                            <span class="relative inline-block">
                                <span class="bg-gradient-to-r from-blue-300 to-purple-300 bg-clip-text text-transparent">
                                    Soporte
                                </span>
                                <span class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full animate-pulse"></span>
                            </span>
                        </h1>

                        <!-- Description -->
                        <p class="mb-8 text-lg text-gray-200 leading-relaxed animate-fade-in-up" style="animation-delay: 0.4s;">
                            Tu portal integral para gestionar incidencias y solicitudes de soporte técnico.
                            <br><span class="font-medium text-white">Reporta problemas, realiza seguimiento y mantente informado del estado de tus tickets.</span>
                        </p>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 mb-8 animate-fade-in-up" style="animation-delay: 0.6s;">
                            <a href="{{ url('/dashboard?view=create') }}"
                               class="inline-flex items-center justify-center rounded-md bg-primary px-8 py-4 text-center text-base font-medium text-white shadow-1 hover:bg-blue-600 disabled:bg-gray-3 disabled:text-dark-5 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                                <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Reportar Incidencia
                            </a>
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center justify-center rounded-md border border-white/20 px-8 py-4 text-center text-base font-medium text-white hover:bg-white/10 backdrop-blur-sm transition-all duration-300 hover:border-white/40 hover:scale-105 group">
                                <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Ver Mis Tickets
                            </a>
                        </div>

                        <!-- Stats Section -->
                        <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 animate-fade-in-up" style="animation-delay: 0.8s;">
                            <div class="text-center">
                                <h3 class="text-3xl font-bold text-white mb-2">98%</h3>
                                <p class="text-sm text-gray-300">Resolución exitosa</p>
                            </div>
                            <div class="text-center">
                                <h3 class="text-3xl font-bold text-white mb-2">24/7</h3>
                                <p class="text-sm text-gray-300">Soporte disponible</p>
                            </div>
                            <div class="text-center sm:col-span-1 col-span-2">
                                <h3 class="text-3xl font-bold text-white mb-2">< 2h</h3>
                                <p class="text-sm text-gray-300">Tiempo respuesta</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right side - Welcome Card -->
                <div class="w-full px-4 lg:w-5/12">
                    <div class="relative animate-fade-in-up" style="animation-delay: 1s;">
                        <div class="mx-auto max-w-[400px] bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-sm rounded-3xl p-8 shadow-2xl border border-white/20">
                            <!-- Welcome Icon -->
                            <div class="mb-6 flex justify-center">
                                <div class="relative p-6 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl shadow-2xl">
                                    <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-emerald-400 rounded-2xl blur-lg opacity-50 animate-pulse"></div>
                                    <svg class="w-12 h-12 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Welcome Message -->
                            <div class="text-center">
                                <h3 class="mb-4 text-2xl font-bold text-white">
                                    ¡Bienvenido de vuelta!
                                </h3>
                                <p class="text-lg text-gray-200 mb-6">
                                    {{ auth()->user()->name }}
                                </p>

                                <!-- Status indicators -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                                        <span class="text-gray-300">Estado de conexión</span>
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                            <span class="text-green-400 text-sm font-medium">Online</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                                        <span class="text-gray-300">Rol de usuario</span>
                                        <span class="text-blue-400 text-sm font-medium">{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Usuario' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    <section class="relative overflow-hidden bg-primary pt-20 pb-20 lg:pb-[90px] lg:pt-[120px] min-h-screen flex items-center">
        <!-- Video Carousel Background -->
        <div class="absolute inset-0 z-0" id="videoCarousel2">
            <!-- Video 1 -->
            <video
                class="video-slide w-full h-full object-cover absolute inset-0 opacity-100 transition-opacity duration-1000"
                autoplay
                muted
                loop
                playsinline
                data-video="1"
                poster="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkyMCIgaGVpZ2h0PSIxMDgwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMTAwJSIgeTI9IjEwMCUiPjxzdG9wIG9mZnNldD0iMCUiIHN0eWxlPSJzdG9wLWNvbG9yOiMzYjgyZjY7c3RvcC1vcGFjaXR5OjEiIC8+PHN0b3Agb2Zmc2V0PSIxMDAlIiBzdHlsZT0ic3RvcC1jb2xvcjojOGIzNWZmO3N0b3Atb3BhY2l0eToxIiAvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZGllbnQpIiAvPjwvc3ZnPg=="
            >
                <source src="{{ asset('videos/background1.mp4') }}" type="video/mp4">
                <source src="{{ asset('videos/background1.webm') }}" type="video/webm">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-digital-abstract-blue-background-4031-large.mp4" type="video/mp4">
            </video>

            <!-- Video 2 -->
            <video
                class="video-slide w-full h-full object-cover absolute inset-0 opacity-0 transition-opacity duration-1000"
                muted
                loop
                playsinline
                data-video="2"
                poster="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkyMCIgaGVpZ2h0PSIxMDgwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQyIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjojOGIzNWZmO3N0b3Atb3BhY2l0eToxIiAvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3R5bGU9InN0b3AtY29sb3I6IzNiODJmNjtzdG9wLW9wYWNpdHk6MSIgLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWRpZW50MikiIC8+PC9zdmc+"
            >
                <source src="{{ asset('videos/background2.mp4') }}" type="video/mp4">
                <source src="{{ asset('videos/background2.webm') }}" type="video/webm">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-tech-devices-background-in-blue-4033-large.mp4" type="video/mp4">
            </video>

            <!-- Video 3 -->
            <video
                class="video-slide w-full h-full object-cover absolute inset-0 opacity-0 transition-opacity duration-1000"
                muted
                loop
                playsinline
                data-video="3"
                poster="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkyMCIgaGVpZ2h0PSIxMDgwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQzIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjojMDU0OTRmO3N0b3Atb3BhY2l0eToxIiAvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3R5bGU9InN0b3AtY29sb3I6IzA2MmU2ZjtzdG9wLW9wYWNpdHk6MSIgLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWRpZW50MykiIC8+PC9zdmc+"
            >
                <source src="{{ asset('videos/background3.mp4') }}" type="video/mp4">
                <source src="{{ asset('videos/background3.webm') }}" type="video/webm">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-network-mesh-4166-large.mp4" type="video/mp4">
            </video>

            <!-- Video 4 -->
            <video
                class="video-slide w-full h-full object-cover absolute inset-0 opacity-0 transition-opacity duration-1000"
                muted
                loop
                playsinline
                data-video="4"
                poster="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTkyMCIgaGVpZ2h0PSIxMDgwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQ0IiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjojNzMxOTZkO3N0b3Atb3BhY2l0eToxIiAvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3R5bGU9InN0b3AtY29sb3I6IzM5MjY4OTtzdG9wLW9wYWNpdHk6MSIgLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWRpZW50NCkiIC8+PC9zdmc+"
            >
                <source src="{{ asset('videos/background4.mp4') }}" type="video/mp4">
                <source src="{{ asset('videos/background4.webm') }}" type="video/webm">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-purple-particles-moving-vertically-26074-large.mp4" type="video/mp4">
            </video>

            <!-- Fallback gradient for when all videos fail -->
            <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-blue-500 via-purple-600 to-indigo-700 animate-gradient-x opacity-0" id="fallbackGradient2"></div>

            <!-- Video Carousel Indicators -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                <div class="video-indicator w-2 h-2 rounded-full bg-white/50 transition-all duration-300" data-video="1"></div>
                <div class="video-indicator w-2 h-2 rounded-full bg-white/30 transition-all duration-300" data-video="2"></div>
                <div class="video-indicator w-2 h-2 rounded-full bg-white/30 transition-all duration-300" data-video="3"></div>
                <div class="video-indicator w-2 h-2 rounded-full bg-white/30 transition-all duration-300" data-video="4"></div>
            </div>

            <!-- Video Carousel Controls -->
            <div class="absolute top-1/2 left-4 transform -translate-y-1/2 z-10">
                <button class="video-prev bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full p-2 transition-all duration-300 group" aria-label="Video anterior">
                    <svg class="w-6 h-6 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <div class="absolute top-1/2 right-4 transform -translate-y-1/2 z-10">
                <button class="video-next bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full p-2 transition-all duration-300 group" aria-label="Siguiente video">
                    <svg class="w-6 h-6 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

            <!-- Overlay gradients - TailGrids style -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/40 to-black/60"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/40"></div>

            <!-- Animated particles -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute w-2 h-2 bg-white/20 rounded-full animate-float" style="top: 20%; left: 10%; animation-delay: 0s;"></div>
                <div class="absolute w-3 h-3 bg-blue-400/30 rounded-full animate-float" style="top: 60%; left: 80%; animation-delay: 2s;"></div>
                <div class="absolute w-1 h-1 bg-purple-400/40 rounded-full animate-float" style="top: 40%; left: 60%; animation-delay: 4s;"></div>
                <div class="absolute w-2 h-2 bg-white/15 rounded-full animate-float" style="top: 80%; left: 30%; animation-delay: 6s;"></div>
                <div class="absolute w-1 h-1 bg-blue-300/50 rounded-full animate-float" style="top: 30%; left: 90%; animation-delay: 8s;"></div>
            </div>
        </div>

        <!-- TailGrids decorative elements -->
        <div class="absolute left-1/2 top-0 -z-10 h-full w-full -translate-x-1/2 pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-32 h-32 border border-white/10 rounded-full animate-pulse-slow"></div>
            <div class="absolute top-1/2 right-1/4 w-24 h-24 border border-blue-400/20 rounded-full animate-spin-slow"></div>
            <div class="absolute bottom-1/4 left-1/3 w-40 h-40 border border-purple-400/15 rounded-full animate-pulse-slow" style="animation-delay: 3s;"></div>
        </div>

        <!-- TailGrids dot pattern -->
        <div class="absolute -top-3 right-0 z-[-1] opacity-30">
            <svg width="134" height="106" viewBox="0 0 134 106" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="1.66667" cy="104" r="1.66667" transform="rotate(-90 1.66667 104)" fill="#8B5CF6" />
                <circle cx="16.3333" cy="104" r="1.66667" transform="rotate(-90 16.3333 104)" fill="#8B5CF6" />
                <circle cx="31" cy="104" r="1.66667" transform="rotate(-90 31 104)" fill="#8B5CF6" />
                <circle cx="45.6667" cy="104" r="1.66667" transform="rotate(-90 45.6667 104)" fill="#8B5CF6" />
                <circle cx="60.3334" cy="104" r="1.66667" transform="rotate(-90 60.3334 104)" fill="#8B5CF6" />
                <circle cx="88.6667" cy="104" r="1.66667" transform="rotate(-90 88.6667 104)" fill="#8B5CF6" />
                <circle cx="117.667" cy="104" r="1.66667" transform="rotate(-90 117.667 104)" fill="#8B5CF6" />
                <circle cx="74.6667" cy="104" r="1.66667" transform="rotate(-90 74.6667 104)" fill="#8B5CF6" />
                <circle cx="103" cy="104" r="1.66667" transform="rotate(-90 103 104)" fill="#8B5CF6" />
                <circle cx="132" cy="104" r="1.66667" transform="rotate(-90 132 104)" fill="#8B5CF6" />
            </svg>
        </div>

        <div class="container mx-auto relative z-10">
            <div class="-mx-4 flex flex-wrap items-center">
                <div class="w-full px-4 lg:w-7/12">
                    <div class="max-w-[570px] pb-10 text-center lg:text-left">
                        <!-- Badge -->
                        <div class="mb-6 inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-500/20 to-orange-500/20 backdrop-blur-sm rounded-full border border-amber-400/30 shadow-lg animate-fade-in-up">
                            <div class="mr-3 relative">
                                <div class="w-3 h-3 bg-amber-400 rounded-full shadow-lg"></div>
                                <div class="absolute inset-0 w-3 h-3 bg-amber-400 rounded-full animate-ping opacity-75"></div>
                            </div>
                            <span class="text-sm font-medium text-amber-100">
                                Acceso Restringido - Solo Empleados
                            </span>
                        </div>

                        <!-- Main Title -->
                        <h1 class="mb-6 text-4xl font-bold leading-[1.2] text-white sm:text-5xl lg:text-6xl animate-fade-in-up" style="animation-delay: 0.2s;">
                            Centro de
                            <span class="relative inline-block">
                                <span class="bg-gradient-to-r from-blue-300 to-purple-300 bg-clip-text text-transparent">
                                    Soporte
                                </span>
                                <span class="absolute -bottom-2 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full animate-pulse"></span>
                            </span>
                        </h1>

                        <!-- Description -->
                        <p class="mb-8 text-lg text-gray-200 leading-relaxed animate-fade-in-up" style="animation-delay: 0.4s;">
                            Tu portal integral para gestionar incidencias y solicitudes de soporte técnico.
                            <br><span class="font-medium text-white">Reporta problemas, realiza seguimiento y mantente informado del estado de tus tickets.</span>
                        </p>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 mb-8 animate-fade-in-up" style="animation-delay: 0.6s;">
                            <button onclick="Livewire.dispatch('openAuthModal', { mode: 'login' })"
                                    class="inline-flex items-center justify-center rounded-md bg-primary px-8 py-4 text-center text-base font-medium text-white shadow-1 hover:bg-blue-600 disabled:bg-gray-3 disabled:text-dark-5 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                                <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Iniciar Sesión
                            </button>
                            <button onclick="Livewire.dispatch('openAuthModal', { mode: 'register' })"
                                    class="inline-flex items-center justify-center rounded-md border border-white/20 px-8 py-4 text-center text-base font-medium text-white hover:bg-white/10 backdrop-blur-sm transition-all duration-300 hover:border-white/40 hover:scale-105 group">
                                <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Registrarse
                            </button>
                        </div>

                        <!-- Features list -->
                        <div class="space-y-3 animate-fade-in-up" style="animation-delay: 0.8s;">
                            <div class="flex items-center text-gray-200">
                                <svg class="w-5 h-5 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Gestión completa de tickets</span>
                            </div>
                            <div class="flex items-center text-gray-200">
                                <svg class="w-5 h-5 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Seguimiento en tiempo real</span>
                            </div>
                            <div class="flex items-center text-gray-200">
                                <svg class="w-5 h-5 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Notificaciones instantáneas</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right side - Access Restriction Card -->
                <div class="w-full px-4 lg:w-5/12">
                    <div class="relative animate-fade-in-up" style="animation-delay: 1s;">
                        <div class="mx-auto max-w-[400px] bg-gradient-to-br from-amber-500/20 to-orange-500/20 backdrop-blur-sm rounded-3xl p-8 shadow-2xl border border-amber-400/30">
                            <!-- Lock Icon -->
                            <div class="mb-6 flex justify-center">
                                <div class="relative p-6 bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl shadow-2xl">
                                    <div class="absolute inset-0 bg-gradient-to-r from-amber-400 to-orange-400 rounded-2xl blur-lg opacity-50 animate-pulse"></div>
                                    <svg class="w-12 h-12 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Access Message -->
                            <div class="text-center">
                                <h3 class="mb-4 text-2xl font-bold text-white">
                                    Acceso Restringido
                                </h3>
                                <p class="text-gray-200 mb-6 leading-relaxed">
                                    Solo <span class="font-bold text-amber-300">empleados de la Municipalidad</span> pueden reportar incidencias y acceder al sistema de soporte.
                                </p>

                                <!-- Security badges -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-center p-3 bg-white/5 rounded-lg">
                                        <svg class="w-5 h-5 mr-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-amber-300 font-medium">Acceso Seguro y Verificado</span>
                                    </div>
                                    <div class="flex items-center justify-center p-3 bg-white/5 rounded-lg">
                                        <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <span class="text-blue-300 font-medium">Protección de Datos</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endauth
