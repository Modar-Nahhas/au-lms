<link rel="stylesheet"
      href="/assets/css/add-edit-book-modal.css">
<!-- MODAL: Add / Edit Book -->
<div class="modal fade" id="bookModal" tabindex="-1" role="dialog" aria-labelledby="bookModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" id="book-form">
            <div class="modal-header">
                <h5 class="modal-title" id="bookModalLabel">Add Book</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <input type="hidden" id="book-id">
                
                <div class="form-group">
                    <label for="bookISBN">ISBN</label>
                    <input type="text" id="bookISBN" class="form-control" placeholder="978-3-16-148410-0" name="isbn">
                    <div class="invalid-feedback d-none"></div>
                </div>
                
                <div class="form-group">
                    <label for="bookTitle">Title</label>
                    <input type="text" id="bookTitle" class="form-control" name="title">
                    <div class="invalid-feedback d-none"></div>
                </div>
                
                <div class="form-group">
                    <label for="bookAuthor">Author</label>
                    <input type="text" id="bookAuthor" class="form-control" name="author">
                    <div class="invalid-feedback d-none"></div>
                </div>
                
                <div class="form-group">
                    <label for="bookPublisher">Publisher</label>
                    <input type="text" id="bookPublisher" class="form-control" name="publisher">
                    <div class="invalid-feedback d-none"></div>
                </div>
                
                <div class="form-group">
                    <label for="bookLanguage">Language</label>
                    <select id="bookLanguage" name="language" class="form-control" name="language">
                        <option value="">Language</option>
                        <?php
                        $languages = \LMS_Website\Enums\LanguageEnum::toArray();
                        foreach ($languages as $lang):
                            ?>
                            <option value="<?= $lang ?>"><?= $lang ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback d-none"></div>
                </div>
                
                <div class="form-group">
                    <label for="bookCategory">Category</label>
                    <select id="bookCategory" class="form-control" name="category">
                        <option value="">Category</option>
                        <?php
                        $categories = \LMS_Website\Enums\CategoryEnum::toArray();
                        foreach ($categories as $cat):
                            ?>
                            <option value="<?= $cat ?>"><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback d-none"></div>
                </div>
                
                <div class="form-group">
                    <label for="bookStatus">Status</label>
                    <select id="bookStatus" class="form-control" name="status">
                        <?php
                        $statuses = \LMS_Website\Enums\BookStatusEnum::toArray();
                        foreach ($statuses as $status):
                            ?>
                            <option value="<?= $status ?>"><?= $status ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback d-none"></div>
                </div>
                
                <div class="form-group description-group">
                    <label for="description">Descriptions</label>
                    <textarea id="description" class="form-control" name="description" rows="3"></textarea>
                    <div class="invalid-feedback d-none"></div>
                </div>
                
                <div class="form-group cover-image-group">
                    <label for="bookCover">Cover Image</label>
                    
                    <div class="custom-file">
                        <input type="file"
                               class="custom-file-input"
                               id="bookCover"
                               name="coverImage"
                               accept="image/*">
                        <label class="custom-file-label" for="bookCover">
                            Choose cover image...
                        </label>
                    </div>
                    
                    <small class="form-text text-muted">
                        Recommended: JPG or PNG, up to 2MB.
                    </small>
                    
                    <div class="mt-3 d-none" id="coverPreviewWrapper">
                        <img id="coverPreview"
                             src="/assets/img/default_book.png"
                             alt="Cover preview"
                             class="img-thumbnail cover-preview">
                    </div>
                    
                    <div class="invalid-feedback d-none"></div>
                </div>
            
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="save-book-btn">Save</button>
            </div>
        </form>
    </div>
</div>