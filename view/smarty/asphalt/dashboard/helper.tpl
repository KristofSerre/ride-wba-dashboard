{function name="renderRegion" region=$region regionWidgets=$regionWidgets}
    <div class="column grid__item" data-region="{$region}">
        <h4>Region {$region}</h4>
        {foreach $regionWidgets as $widgetId => $widget}
            <div class="widget widget-{$widgetId} clearfix" data-widget="{$widgetId}">
                <div class="widget__header">
                    <div class="widget__actions text-right dropdown">
                        <a href="#" class="dropdown" data-toggle="dropdown"><i class="icon icon--cog"></i></a>
                        <ul class="dropdown__menu dropdown__menu--right">
                            <li>
                                <a href="{url id="admin.dashboard.properties" parameters=["widget" => $widgetId]}">
                                    <i class="icon icon--gears"> {"label.properties"|translate}</i>
                                </a>
                            </li>
                            <li class="dropdown__divider"></li>
                            <li>
                                <a href="{url id="admin.dashboard.remove" parameters=["widget" => $widgetId]}" data-confirm="Are you sure you want to delete this widget?">
                                    <i class="icon icon--remove"> {"label.delete"|translate}</i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="widget__content">
                    {$widget}
                </div>
            </div>
        {/foreach}
    </div>
{/function}