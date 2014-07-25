{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblNavigationBlock|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}

	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td id="leftColumn">

                        <div class="box">
                            <div class="heading">
                                <h3>
                                    <label for="internalPage">{$lblPage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                                </h3>
                            </div>
                            <div class="options">
                                {$ddmPageId} {$ddmPageIdError}
                            </div>
                        </div>

                        <div class="box">
                            <div class="heading">
                                <label for="class">{$lblDescription|ucfirst}</label>
                            </div>
                            <div class="options">
                                {$txtDescription} {$txtDescriptionError}
                            </div>

                            <p></p>

                            <div>
                                <div class="heading">
                                    <label for="class">{$lblClass|ucfirst}</label>
                                </div>
                                <div class="options">
                                    {$txtClass} {$txtClassError}
                                </div>
                            </div>

                            <p></p>

                            <div>
                                <div class="heading">
                                    <label for="recursionLevel">{$lblRecursionLevel|ucfirst}</label>
                                </div>
                                <div class="options">
                                    {$ddmRecursionLevel} {$ddmRecursionLevelError}
                                </div>
                            </div>
                        </div>

                    </td>

					<td id="sidebar">

							<div class="box">
								<div class="heading">
									<h3>
										<label for="categoryId">{$lblCategory|ucfirst}</label>
									</h3>
								</div>
								<div class="options">
									{$ddmCategoryId} {$ddmCategoryIdError}
								</div>
							</div>


					</td>
				</tr>
			</table>
		</div>

	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblPublish|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
