<div class="widget">
    {if $result}
        <h3>{"label.modified.last"|translate}</h3>
        <div class="grid grid--bp-med-2-col">
            <div class="grid__item">
                <strong>{"label.entry"|translate}</strong>
            </div>
            <div class="grid__item">
                <strong>{"label.changes"|translate}</strong>
            </div>
            {foreach $result as $content}
                   <div class="grid__item">
                       <a href={*"{url id="system.orm.scaffold.action.entry" parameters=['model' => $content->getModel(), 'locale' => $locale, 'id' => $content->getEntry(), 'action' => 'detail']}"*}>
                           {$content->getModel()} ({$content->getDateModified()|date_format:"%d/%m/%y %X"})
                       </a>
                   </div>
                   <div class="grid__item">{$content->getChanges()|count}
                   </div>
            {/foreach}
        </div>
        <hr/>
    {/if}
    <h3>{"label.models.new"|translate}</h3>
    <ul class="list--unstyled">
        {foreach $models as $model}
        <li>
            <a href="/admin/system/orm/model/{$model->getName()}/scaffold/{$locale}">
                {"title."|cat:{$model->getName()|lower}|translate}
            </a>
        </li>
        {/foreach}
    </ul>



</div>