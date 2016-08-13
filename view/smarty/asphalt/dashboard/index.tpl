{extends file="base/index"}

{block name="head_title" prepend}{translate key="title.dashboard"} - {/block}

{block name="content_body"}
<div class="dashboard">
    <div class="grid section">
        <div class="grid__12 section__content">
            {foreach $layout as $region => $regionWidgets}
                {if $region == 1}
                    <h4>Region {$region}
                        <span class="region__actions text-right dropdown">
                            <a href="#" class="dropdown" data-toggle="dropdown"><i class="icon icon--plus-square"></i></a>
                            <ul class="dropdown__menu dropdown__menu--right">
                                {foreach $widgets as $widgetId => $widget}
                                    <li>
                                        <a href="{url id="admin.dashboard.add" parameters=["region" => {$region}, "widget" => $widgetId]}">
                                            {$widgetId} <i class="icon icon--plus"></i>
                                        </a>
                                    </li>
                                {/foreach}
                            </ul>
                        </span>
                    </h4>
                    {foreach $regionWidgets as $widgetId => $widget}
                        <div class="widget widget-{$widgetId} clearfix" data-widget="{$widgetId}">
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
                            <div class="widget__content">
                                {$widget}
                            </div>
                        </div>
                    {/foreach}
                {/if}
            {/foreach}
        </div>
    </div>
    <div class="grid grid--bp-med-3-col block widgets">
        {foreach $layout as $region => $regionWidgets}
            {if $region !== 1}
                <div class="column grid__item" data-region="{$region}">
                    <h4>Region {$region}
                        <span class="region__actions text-right dropdown">
                            <a href="#" class="dropdown" data-toggle="dropdown"><i class="icon icon--plus-square"></i></a>
                            <ul class="dropdown__menu dropdown__menu--right">
                                {foreach $widgets as $widgetId => $widget}
                                    <li>
                                        <a href="{url id="admin.dashboard.add" parameters=["region" => {$region}, "widget" => $widgetId]}">
                                            {$widgetId} <i class="icon icon--plus"></i>
                                        </a>
                                    </li>
                                {/foreach}
                            </ul>
                        </span>
                    </h4>
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
            {/if}
        {/foreach}
    </div>
</div>
{/block}

{function RenderRegion region=$region regionWidget=$regionWidgets}

{/function}

{block name="styles" append}
    <link href="{$app.url.base}/asphalt/css/dashboard.css" rel="stylesheet" media="screen">
{/block}

{block name="scripts" append}
    <script src="{$app.url.base}/asphalt/js/dashboard.js"></script>
{/block}
