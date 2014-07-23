{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblNavigationBlock|ucfirst}: {$lblAddCategory}</h2>
</div>

{form:addCategory}
	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<div class="box">
				<div class="heading">
					<h3><label for="title">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label></h3>
				</div>
				<div class="options">
					{$txtTitle} {$txtTitleError}
				</div>
			</div>

            <div class="box">
                <div class="heading">
                    <h3><label for="alias">{$lblAlias|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label></h3>
                </div>
                <div class="options">
                    {$txtAlias} {$txtAliasError}
                </div>
            </div>
        </div>

	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddCategory|ucfirst}" />
		</div>
	</div>
{/form:addCategory}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}