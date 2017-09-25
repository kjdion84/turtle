                    @can('Browse bread_model_strings')
                        <li class="nav-item{{ request()->is('bread_model_variables') ?  ' active' : '' }}"><a class="nav-link" href="{{ route('bread_model_variables') }}">bread_model_strings</a></li>
                    @endcan