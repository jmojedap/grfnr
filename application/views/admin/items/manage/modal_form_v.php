<!-- Modal -->
<div class="modal fade" id="modalFormCenter" tabindex="-1" role="dialog" aria-labelledby="modalFormCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalFormCenterTitle">
            {{ currCategory.item_name }}:
            {{ formConfig.title }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php $this->load->view($this->views_folder . 'manage/form_v'); ?>
      </div>
    </div>
  </div>
</div>