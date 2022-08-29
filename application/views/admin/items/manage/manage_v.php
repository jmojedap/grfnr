<div id="manageItemsApp">
    <div class="row">
        <div class="col-md-4">
            <div class="input-group mb-3">
                <input
                    name="q" type="text" class="form-control" v-on:change="getCategories"
                    required
                    title="Categoría" placeholder="Categoría..."
                    v-model="filters.q"
                >
                <div class="input-group-append">
                    <button class="btn btn-light" type="button" v-on:click="clearFilters">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <table class="table table-sm bg-white">
                <tbody>
                    <tr v-for="(category, key_c) in categories"
                        v-bind:class="{'table-info': category.cod == currCategory.cod}">
                        <td width="50px" class="text-center">{{ category.cod }}</td>
                        <td>{{ category.item_name }}</td>
                        <td width="50px">
                            <button class="a4" v-on:click="setCategory(key_c)">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col col-md-8">
            <div v-bind:style="`margin-top: ` + marginValue + `px;`" v-show="categories.length > 0">
                <div class="card mb-2">
                    <table class="table table-sm bg-white">
                        <thead>
                            <th width="50px">Cód.</th>
                            <th>Nombre ítem</th>
                            <th width="75px"></th>
                        </thead>
                        <tbody>
                            <tr v-for="(row, key) in items" v-bind:class="{'table-info':rowId == row.id}">
                                <td class="text-center">{{ row.cod }}</td>
                                <td v-bind:class="`td_level_` + row.level">
                                    <span v-bind:class="`item_level_` + row.level">
                                        {{ row.item_name }}
                                    </span>
                                </td>
                                <td>
                                    <button class="a4" v-on:click="loadFormValues(key)" data-toggle="modal" data-target="#modalFormCenter">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                    <button class="a4" data-toggle="modal" data-target="#delete_modal"
                                        v-on:click="setCurrentElement(key)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-success w120p" v-on:click="clearForm" data-toggle="modal" data-target="#modalFormCenter">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <?php $this->load->view($this->views_folder . 'manage/modal_form_v'); ?>
    <?php $this->load->view('common/modal_single_delete_v'); ?>
</div>

<?php $this->load->view($this->views_folder . 'manage/vue_v'); ?>