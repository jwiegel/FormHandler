<?php

declare(strict_types=1);

function delete_directory($dirname)
{
    if (is_dir($dirname))
        $dir_handle = opendir($dirname);
    if (!$dir_handle)
        return false;
    while ($file = readdir($dir_handle))
    {
        if ($file != "." && $file != "..")
        {
            if (!is_dir($dirname . "/" . $file))
                unlink($dirname . "/" . $file);
            else
                delete_directory($dirname . '/' . $file);
        }
    }
    closedir($dir_handle);
    rmdir($dirname);
    return true;
}

final class formhandler_UploadFieldTest extends FormhandlerTestCase
{
    private ?string $_tempPath = null;
    protected function setUp(): void
    {
        parent::setUp();

        $this->_tempPath = sys_get_temp_dir() . "/uploadtest";

        if (is_dir($this->_tempPath))
            delete_directory($this->_tempPath);
        mkdir($this->_tempPath);
    }
    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield");

        static::assertEmpty($form->getValue("uploadfield"));

        static::assertFormFlushContains($form, ['Uploadfield:<input type="file" name="uploadfield" id="uploadfield" onchange="fh_checkUpload(this, \'jpg jpeg png gif doc txt bmp tif tiff pdf\', \'Only the following extensions are allowed: %s.\')"  />error_uploadfield']);
    }

    public function test_new_config(): void
    {
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg"
        );

        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertEmpty($form->getValue("uploadfield"));

        static::assertFormFlushContains($form, ['Uploadfield:<input type="file" name="uploadfield" id="uploadfield" onchange="fh_checkUpload(this, \'jpg jpeg jpe\', \'Only the following extensions are allowed: %s.\')"  />error_uploadfield']);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.jpg',
                'size'     => 8009,
                'tmp_name' => dirname(__FILE__) . "/test.jpg",
                'type'     => 'image/jpeg'
            ]
        ];
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg",
            "name" => "uploaded",
            "path" => $this->_tempPath
        );

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertTrue($form->isUploaded("uploadfield"));
        static::assertEquals("uploaded.jpg", $form->getValue("uploadfield"));

        $data  = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $this->setPrivateProperty($form, "_unittestmode", true);
        static::assertFlush($form);

        static::assertTrue(unlink("{$this->_tempPath}/uploaded.jpg"));
    }

    public function test_posted_failure_size(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.jpg',
                'size'     => 8009,
                'tmp_name' => dirname(__FILE__) . "/test.jpg",
                'type'     => 'image/jpeg'
            ]
        ];
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg",
            "name" => "uploaded",
            "path" => $this->_tempPath,
            "size" => 100
        );

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertTrue($form->isUploaded("uploadfield"));
        static::assertEquals("uploaded.jpg", $form->getValue("uploadfield"));

        $data  = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $this->setPrivateProperty($form, "_unittestmode", true);

        static::assertFormFlushContains($form, [
            'Uploadfield:<input type="file" name="uploadfield" id="uploadfield" onchange="fh_checkUpload(this, \'jpg jpeg jpe\', \'Only the following extensions are allowed: %s.\')"  class="error" />error_uploadfield',
            '<span id="error_uploadfield" class="error">Maximum file size of 0.1 kb exceeded</span>'
        ]);
    }

    public function test_posted_failure_mime(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => basename(__FILE__),
                'size'     => filesize(__FILE__),
                'tmp_name' => __FILE__,
                'type'     => mime_content_type(__FILE__)
            ]
        ];
        $config = array(
            "type" => "php",
            "mime" => "text/plain",
            "name" => "uploaded",
            "path" => $this->_tempPath
        );

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertTrue($form->isUploaded("uploadfield"));
        //static::assertEquals($_FILES['uploadfield']['name'], $form->getValue("uploadfield"));

        $data  = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $this->setPrivateProperty($form, "_unittestmode", true);

        static::assertFormFlushContains($form, [
            'Uploadfield:<input type="file" name="uploadfield" id="uploadfield" onchange="fh_checkUpload(this, \'php\', \'Only the following extensions are allowed: %s.\')"  class="error" />error_uploadfield',
            '<span id="error_uploadfield" class="error">The uploaded file is of an invalid file type!</span>'
        ]);
    }

    public function test_posted_failure_extension(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => basename(__FILE__),
                'size'     => filesize(__FILE__),
                'tmp_name' => __FILE__,
                'type'     => mime_content_type(__FILE__)
            ]
        ];
        $config = array(
            "type" => "txt",
            "mime" => "text/x-php",
            "name" => "uploaded",
            "path" => $this->_tempPath
        );

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertTrue($form->isUploaded("uploadfield"));
        //static::assertEquals($_FILES['uploadfield']['name'], $form->getValue("uploadfield"));

        $data  = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $this->setPrivateProperty($form, "_unittestmode", true);

        static::assertFormFlushContains($form, [
            'Uploadfield:<input type="file" name="uploadfield" id="uploadfield" onchange="fh_checkUpload(this, \'txt\', \'Only the following extensions are allowed: %s.\')"  class="error" />error_uploadfield',
            '<span id="error_uploadfield" class="error">Only the following extensions are allowed: txt.</span>'
        ]);
    }

    public function test_posted_failure_width(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.jpg',
                'size'     => 8009,
                'tmp_name' => dirname(__FILE__) . "/test.jpg",
                'type'     => 'image/jpeg'
            ]
        ];
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg",
            "name" => "uploaded",
            "path" => $this->_tempPath,
            "width" => 100,
            "height" => 100
        );

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertTrue($form->isUploaded("uploadfield"));
        static::assertEquals("uploaded.jpg", $form->getValue("uploadfield"));

        $data  = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $this->setPrivateProperty($form, "_unittestmode", true);

        static::assertFormFlushContains($form, [
            'Uploadfield:<input type="file" name="uploadfield" id="uploadfield" onchange="fh_checkUpload(this, \'jpg jpeg jpe\', \'Only the following extensions are allowed: %s.\')"  class="error" />error_uploadfield',
            '<span id="error_uploadfield" class="error">The dimension of the image can be 100 x 100 or less. The uploaded file has a dimension of 291 x 139!</span>'
        ]);
    }

    public function test_posted_failure_override(): void
    {
        // copy src to uploadfolder
        static::assertTrue(copy(dirname(__FILE__) . "/test.jpg", "{$this->_tempPath}/uploaded.jpg"));

        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.jpg',
                'size'     => 8009,
                'tmp_name' => dirname(__FILE__) . "/test.jpg",
                'type'     => 'image/jpeg'
            ]
        ];
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg",
            "name" => "uploaded",
            "path" => $this->_tempPath
        );

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertTrue($form->isUploaded("uploadfield"));
        static::assertEquals("uploaded.jpg", $form->getValue("uploadfield"));

        $data  = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $this->setPrivateProperty($form, "_unittestmode", true);
        static::assertFormFlushContains($form, '<span id="error_uploadfield" class="error">The file you tried to upload already exists!</span>');
    }

    public function test_posted_rename(): void
    {
        // copy src to uploadfolder
        static::assertTrue(copy(dirname(__FILE__) . "/test.jpg", "{$this->_tempPath}/uploaded.jpg"));

        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.jpg',
                'size'     => 8009,
                'tmp_name' => dirname(__FILE__) . "/test.jpg",
                'type'     => 'image/jpeg'
            ]
        ];
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg",
            "name" => "uploaded",
            "path" => $this->_tempPath,
            "exists" => "rename"
        );

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertTrue($form->isUploaded("uploadfield"));
        static::assertEquals("uploaded(1).jpg", $form->getValue("uploadfield"));

        $data  = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $this->setPrivateProperty($form, "_unittestmode", true);
        static::assertFlush($form);

        static::assertTrue(unlink("{$this->_tempPath}/uploaded.jpg"));
        static::assertTrue(unlink("{$this->_tempPath}/uploaded(1).jpg"));
    }

    public function test_posted_override(): void
    {
        // copy src to uploadfolder
        static::assertTrue(copy(dirname(__FILE__) . "/test.jpg", "{$this->_tempPath}/uploaded.jpg"));

        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.jpg',
                'size'     => 8009,
                'tmp_name' => dirname(__FILE__) . "/test.jpg",
                'type'     => 'image/jpeg'
            ]
        ];
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg",
            "name" => "uploaded",
            "path" => $this->_tempPath,
            "exists" => "overwrite"
        );

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertTrue($form->isUploaded("uploadfield"));
        static::assertEquals("uploaded.jpg", $form->getValue("uploadfield"));

        $data  = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $this->setPrivateProperty($form, "_unittestmode", true);
        static::assertFlush($form);

        static::assertTrue(unlink("{$this->_tempPath}/uploaded.jpg"));
    }

    public function test_posted_fillvalue_byinvalid(): void
    {
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg",
            "path" => $this->_tempPath
        );

        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.jpg',
                'size'     => 8009,
                'tmp_name' => dirname(__FILE__) . "/test.jpg",
                'type'     => 'image/jpeg'
            ]
        ];

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);
        $form->textField("TextForFailure", "textforfailure");

        static::assertTrue($form->isUploaded("uploadfield"));

        $data  = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $form->setError("textforfailure", "isfails");

        static::assertFormFlushContains($form, [
            'Uploadfield:<input type="file" name="uploadfield" id="uploadfield" onchange="fh_checkUpload(this, \'jpg jpeg jpe\', \'Only the following extensions are allowed: %s.\')"  class="error" />error_uploadfield',
            "<span id=\"error_uploadfield\" class=\"error\">Because the form isn't valid the file <b>test.jpg</b> has to be selected again if you want to send it with this form!<br />"
        ]);
    }

    public function test_required(): void
    {
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg",
            "required" => true
        );

        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_NO_FILE,
                'name'     => '',
                'size'     => 0,
                'tmp_name' => "",
                'type'     => ""
            ]
        ];

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config, FH_NOT_EMPTY);

        static::assertEmpty($form->getValue("uploadfield"));

        $t = $form->catchErrors(false);

        static::assertEquals(
            '<span id="error_uploadfield" class="error">You did not enter a correct value for this field!</span>',
            $t['uploadfield']
        );
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", array(), null, 'data-old="123"');

        static::assertEmpty($form->getValue("uploadfield"));

        static::assertFormFlushContains($form, ['Uploadfield:<input type="file" name="uploadfield" id="uploadfield" onchange="fh_checkUpload(this, \'jpg jpeg png gif doc txt bmp tif tiff pdf\', \'Only the following extensions are allowed: %s.\')" data-old="123" />error_uploadfield']);
    }

    public function test_resizeImage(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.jpg',
                'size'     => 8009,
                'tmp_name' => dirname(__FILE__) . "/test.jpg",
                'type'     => 'image/jpeg'
            ]
        ];
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg",
            "name" => "uploaded",
            "path" => $this->_tempPath
        );

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertTrue($form->isUploaded("uploadfield"));

        $data  = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $form->resizeImage("uploadfield", $this->_tempPath . "/resize" . $form->getValue("uploadfield"), 30, 30);

        $this->setPrivateProperty($form, "_unittestmode", true);
        static::assertFlush($form);

        $image_info = getimagesize("{$this->_tempPath}/resizeuploaded.jpg");

        static::assertEquals(30, $image_info[0]);

        static::assertTrue(unlink("{$this->_tempPath}/uploaded.jpg"));
        static::assertTrue(unlink("{$this->_tempPath}/resizeuploaded.jpg"));
    }

    public function test_mergeImage(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_FILES = [
            'uploadfield' => [
                'error'    => UPLOAD_ERR_OK,
                'name'     => 'test.jpg',
                'size'     => 8009,
                'tmp_name' => dirname(__FILE__) . "/test.jpg",
                'type'     => 'image/jpeg'
            ]
        ];
        $config = array(
            "type" => "jpg jpeg jpe",
            "mime" => "image/jpeg image/jpg",
            "name" => "uploadedandmerged",
            "path" => $this->_tempPath
        );

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->uploadField("Uploadfield", "uploadfield", $config);

        static::assertTrue($form->isUploaded("uploadfield"));

        $data = $form->GetFileInfo("uploadfield");

        static::assertEquals($_FILES['uploadfield'], $data);

        $form->mergeImage("uploadfield", dirname(__FILE__) . "/mergetext.png");

        $this->setPrivateProperty($form, "_unittestmode", true);
        static::assertFlush($form);

        $filesize = filesize("{$this->_tempPath}/uploadedandmerged.jpg");
        $image_info = getimagesize("{$this->_tempPath}/uploadedandmerged.jpg");


        $filesize_orig = filesize($_FILES['uploadfield']['tmp_name']);
        $image_info_orig = getimagesize($_FILES['uploadfield']['tmp_name']);

        static::assertNotEquals($filesize_orig, $filesize);
        static::assertEquals($image_info_orig, $image_info);

        static::assertTrue(unlink("{$this->_tempPath}/uploadedandmerged.jpg"));
    }

    public function testIsUploaded_expectedError(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isUploaded('upload'));

        $this->assertTriggertError('Error, the uploadfield "upload" does not exists!', E_USER_NOTICE);
    }
    
    public function testIsUploaded_expectedError2(): void
    {
        $form = new FormHandler();

        $form->textField("Upload", "upload");

        static::assertFalse($form->isUploaded('upload'));

        $this->assertTriggertError('Error, the field "upload" is not an uploadfield!', E_USER_NOTICE);
    }

    public function testGetFileInfo_expectedError(): void
    {
        $form = new FormHandler();

        static::assertCount(0, $form->getFileInfo('upload'));
        $this->assertTriggertError('Error, the uploadfield "upload" does not exists!', E_USER_NOTICE);
    }
    
    public function testGetFileInfo_expectedError2(): void
    {
        $form = new FormHandler();

        $form->textField("Upload", "upload");

        static::assertCount(0, $form->getFileInfo('upload'));
        $this->assertTriggertError('Error, the field "upload" is not an uploadfield!', E_USER_NOTICE);
    }
};
