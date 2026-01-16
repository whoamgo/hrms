@php
    $user = auth()->user();
    if (!$user) {
        $menuItems = collect([]);
    } else {
        $roleSlug = $user->role ? $user->role->slug : null;
        
        // Get menu items from cache or database
        $menuItems = \Illuminate\Support\Facades\Cache::remember('user_menu_' . $user->id, 3600, function() use ($user, $roleSlug) {
            if (!$user->role) {
                return collect([]);
            }
            
            return \App\Models\MenuItem::whereHas('roles', function($q) use ($user) {
                $q->where('roles.id', $user->role_id);
            })
            ->orWhereDoesntHave('roles')
            ->active()
            ->byType($roleSlug)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->with(['children' => function($q) use ($user) {
                $q->active()->orderBy('order');
            }])
            ->get();
        });
    }
@endphp

@foreach($menuItems as $menuItem)
    @php
        $menuUrl = '#';
        if ($menuItem->route) {
            try {
                $menuUrl = route($menuItem->route);
            } catch (\Exception $e) {
                $menuUrl = $menuItem->url ?? '#';
            }
        } else {
            $menuUrl = $menuItem->url ?? '#';
        }
    @endphp
    @if($menuItem->children->count() > 0)
        <li>
            <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="{{ $menuItem->icon }}"></i>
                <span>{{ $menuItem->title }}</span>
            </a>
            <ul class="sub-menu" aria-expanded="false">
                @foreach($menuItem->children as $child)
                    @php
                        $childUrl = '#';
                        if ($child->route) {
                            try {
                                $childUrl = route($child->route);
                            } catch (\Exception $e) {
                                $childUrl = $child->url ?? '#';
                            }
                        } else {
                            $childUrl = $child->url ?? '#';
                        }
                    @endphp
                    <li>
                        <a href="{{ $childUrl }}" @if($childUrl == '#') onclick="alert('{{ $child->title }} page coming soon.'); return false;" @endif>
                            {{ $child->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    @else
        <li class="{{ $menuItem->route && request()->routeIs($menuItem->route) ? 'mm-active' : '' }}">
            <a href="{{ $menuUrl }}" @if($menuUrl == '#' && !$menuItem->route) onclick="alert('{{ $menuItem->title }} page coming soon.'); return false;" @endif>
                <i class="{{ $menuItem->icon }}"></i>
                <span>{{ $menuItem->title }}</span>
            </a>
        </li>
    @endif
@endforeach

@if(auth()->check() && auth()->user())
<li>
    <a href="{{ route('notifications.index') }}">
        <i class="mdi mdi-bell-outline"></i>
        <span>Notifications</span>
        @php
            $unreadCount = auth()->user() ? auth()->user()->unreadNotifications()->count() : 0;
        @endphp
        @if($unreadCount > 0)
            <span class="badge badge-danger float-right">{{ $unreadCount }}</span>
        @endif
    </a>
</li>
@endif

<li>
    <a href="#" id="logout-btn">
        <i class="mdi mdi-logout"></i>
        <span>Logout</span>
    </a>
</li>

