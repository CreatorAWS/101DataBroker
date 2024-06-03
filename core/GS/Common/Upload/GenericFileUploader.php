<?php

	namespace GS\Common\Upload;

	use GS\Common\Strings;
	use System;

	class GenericFileUploader
		{
			protected $upload_file_pointer;
			protected $upload_path='';

			function __construct(?array $upload_file_pointer)
				{
					$this->upload_file_pointer=$upload_file_pointer;
					$this->upload_path=System::TempDirPath().md5(microtime(true).mt_rand());
				}

			function __destruct()
				{
					if (Strings::isBeginningEquals(System::TempDirPath(), $this->DestinationPath()) && $this->DestinationFileExists())
						$this->EraseFile();
				}

			function DestinationPath(): string
				{
					return $this->upload_path;
				}

			function DestinationFileExists(): bool
				{
					return file_exists($this->DestinationPath());
				}

			function CopyTo(string $full_file_path): bool
				{
					if ($this->DestinationFileExists())
						{
							if (file_exists($full_file_path))
								unlink($full_file_path);
							copy($this->DestinationPath(), $full_file_path);
							if (file_exists($full_file_path))
								return true;
							else
								return false;
						}
					return false;
				}

			function FileExtension(): string
				{
					return (string)pathinfo($this->GetOriginalName(), PATHINFO_EXTENSION);
				}

			function GetOriginalName(): string
				{
					$filename=$this->upload_file_pointer['name'] ?? '';
					$ext=pathinfo($filename, PATHINFO_EXTENSION);
					if (empty($ext))
						{
							$tmp=explode('/', $this->GetMetaType());
							if (!empty($tmp[1]))
								$ext=$tmp[1];
							else
								$ext=$tmp[0];
							if (!empty($ext))
								$filename.='.'.$ext;
						}
					return $filename;
				}

			function GetMetaType(): string
				{
					return $this->upload_file_pointer['type'] ?? '';
				}

			function MoveUploadedFile(): bool
				{
					$fs = $this->upload_file_pointer['tmp_name'] ?? '';
					if (!empty($fs))
						{
							$fd = $this->DestinationPath();
							if (file_exists($fd))
								unlink($fd);
							$res = move_uploaded_file($fs, $fd);
							if ($res !== false)
								return true;
							else
								return false;
						}
					else
						return false;
				}

			function isUploadOperationSuccessful(): bool
				{
					if (!array_key_exists('error', $this->upload_file_pointer))
						return false;
					else
						return $this->upload_file_pointer['error'] == UPLOAD_ERR_OK;
				}

			function isFilePresentOnUploadOperation(): bool
				{
					if (!$this->isFileVarnameExists())
						return false;
					return $this->upload_file_pointer['error'] != UPLOAD_ERR_NO_FILE;
				}

			function isFileVarnameExists(): bool
				{
					if (!is_array($_FILES))
						return false;
					elseif ($this->upload_file_pointer===NULL)
						return false;
					else
						return true;
				}

			function EraseFile(): void
				{
					if ($this->DestinationFileExists())
						unlink($this->DestinationPath());
				}

			function FileSize(): int
				{
					if (file_exists($this->DestinationPath()))
						return filesize($this->DestinationPath());
					else
						return 0;
				}

		}
