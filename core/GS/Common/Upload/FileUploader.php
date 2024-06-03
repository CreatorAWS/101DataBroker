<?php

	namespace GS\Common\Upload;

	class FileUploader extends GenericFileUploader
		{

			function __construct(string $upload_file_var)
				{
					parent::__construct($_FILES[$upload_file_var] ?? NULL);
				}

			public static function UploadToTemp(string $upload_file_var): FileUploader
				{
					$uploader=new FileUploader($upload_file_var);
					if ($uploader->isUploadOperationSuccessful())
						$uploader->MoveUploadedFile();
					return $uploader;
				}


		}
