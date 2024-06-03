<?php

	namespace GS\Storage\Universal;
	
	use GS\Common\DB\DBQuery;
	use GS\Common\Download\DownloadHelper;
	use GS\Common\GenericMetadataTrait;
	use GS\Common\Strings;
	use GS\Common\SystemSettings;
	use GS\Common\Upload\Extensions\FileExtension;
	use GS\Common\Upload\GenericFileUploader;
	use GS\Media\Images\Tools\ImageTool;
	use GS\ORM\EntityObject;
	use System;

	/**
	 * @method static self initNotExistent()
	 * @method static self UsingCache($id)
	 */
	class StoredFile extends EntityObject
		{
			protected $table_name;

			use GenericMetadataTrait;

			public static function TableName(): string
				{
					return 'file_storage';
				}

			public static function IdFieldName(): string
				{
					return 'id';
				}

			public static function TitleFieldName(): ?string
				{
					return 'title';
				}

			public static function initWithWWWAttachmentMaskedURL($id): self
				{
					$str=substr(md5('attachment'.substr($id, 10)), 0, 10);
					if ($str==substr($id, 0, 10))
						{
							$id=intval(substr($id, 10));
							return new self($id);
						}
					else
						return StoredFile::initNotExistent();
				}

			function AutoDetectMimeTypeByExtension(): void
				{
					if (Strings::isEqualCaseInsensitive($this->FileExtension(), FileExtension::JPG))
						$this->SetMimeType('image/jpeg');
					elseif (Strings::isEqualCaseInsensitive($this->FileExtension(), FileExtension::PNG))
						$this->SetMimeType('image/png');
					elseif (Strings::isEqualCaseInsensitive($this->FileExtension(), FileExtension::HEIC))
						$this->SetMimeType('image/heic');
					elseif (Strings::isEqualCaseInsensitive($this->FileExtension(), FileExtension::PDF))
						$this->SetMimeType('application/pdf');
					elseif (Strings::isEqualCaseInsensitive($this->FileExtension(), FileExtension::GIF))
						$this->SetMimeType('image/gif');
					else
						$this->SetMimeType('application/octet-stream');
				}

			protected function RemoveLocalFile(): void
				{
					$this->EraseFile();
					$this->EraseThumb();
				}

			protected function RemoveDBRecords(): void
				{
					$this->RemoveAllMetaData();
					DBQuery::ForTable($this->table_name)
						   ->AddInt($this->id_field, $this->ID())
						   ->Remove();
				}

			function Remove(): void
				{
					$this->RemoveLocalFile();
					$this->RemoveDBRecords();
					$this->AfterRemove();
				}

			protected static function InitFileRecord(string $object_type, int $object_id): self
				{
					/** @noinspection PhpIncompatibleReturnTypeInspection */
					return StoredFile::CreateObjectWithParams(
						self::TableName(),
						[
							'object_type'=>$object_type,
							'object_id'=>intval($object_id),
						]
					);
				}

			protected function OnCreateFileChecks(): void
				{}

			public static function CreateByCopyingFile($object_type, $object_id, $file_path): self
				{
					$file=StoredFile::InitFileRecord($object_type, $object_id);
					$file->AddFileByCopying($file_path);
					$file->OnCreateFileChecks();
					return $file;
				}

			public static function CreateFromFileUploader(string $object_type, int $object_id, GenericFileUploader $file_uploader): self
				{
					$file=StoredFile::InitFileRecord($object_type, $object_id);
					$file->AddFileByCopying($file_uploader->DestinationPath());
					$file->SetUploadedFileName($file_uploader->GetOriginalName());
					$file->SetMimeType($file_uploader->GetMetaType());
					$file->OnCreateFileChecks();
					return $file;
				}

			public static function CreateCustomURL(string $custom_url, string $object_type, int $object_id): self
				{
					$file=StoredFile::InitFileRecord($object_type, $object_id);
					$file->SetCustomURL($custom_url);
					$file->SetUploadedFileName(basename($custom_url));
					$file->SetStorageLocationTag(StorageLocation::CUSTOM_THIRD_PARTY_URL);
					$file->OnCreateFileChecks();
					return $file;
				}

			protected function EraseFile()
				{
					if (!empty($this->FilePath()) && $this->FileExists())
						unlink($this->FilePath());
				}

			protected function EraseThumb()
				{
					if (!empty($this->ThumbFilePath()) && $this->ThumbFileExists())
						unlink($this->ThumbFilePath());
				}

			public function TypeTag(): string
				{
					return $this->FieldStringValue('object_type');
				}

			public function ChangeTypeTag(string $new_type_tag, ?int $new_object_id=NULL): void
				{
					$filename=$this->FilePath();
					$upd=['object_type'=>$new_type_tag];
					if ($new_object_id!==NULL)
						$upd['object_id']=intval($new_object_id);
					$this->UpdateValues($upd);
					rename($filename, $this->FilePath());
				}

			public function ChangeObjectID(int $new_object_id): void
				{
					$filename=$this->FilePath();
					if ($new_object_id!==NULL)
						$this->UpdateValues(['object_id'=>intval($new_object_id)]);
					rename($filename, $this->FilePath());
				}

			public function UploadedFileName(): string
				{
					return $this->FieldStringValue('uploadedfilename');
				}

			public function SetUploadedFileName(string $val): void
				{
					$this->UpdateValues(['uploadedfilename'=>$val]);
				}

			public function SetMimeType(string $val): void
				{
					$this->UpdateValues(['mime_type'=>$val]);
				}

			public function MimeType(): string
				{
					return $this->FieldStringValue('mime_type');
				}

			public function isImage(): bool
				{
					return in_array($this->FileExtension(), ['jpg', 'jpeg', 'png', 'gif']);
				}

			public function UploadedTimeStamp(): int
				{
					return $this->FieldIntValue('uploadedtime');
				}

			public function ObjectID(): int
				{
					return $this->FieldIntValue('object_id');
				}

			protected function FileExists(): bool
				{
					return file_exists($this->FilePath());
				}

			protected function SaveFileSize(): void
				{
					if ($this->FileExists())
						{
							$size=intval(@filesize($this->FilePath()));
							$this->SetFileSize($size);
						}
				}

			protected function SetFileSize(int $file_size): void
				{
					$this->UpdateValues(['file_size'=>intval($file_size)]);
				}

			protected function SaveThumbFileSize(): void
				{
					if ($this->ThumbFileExists())
						{
							$size=intval(@filesize($this->ThumbFilePath()));
							$this->SetThumbFileSize($size);
						}
				}

			public function FileSize(): int
				{
					return $this->FieldIntValue('file_size');
				}

			public function ThumbFileSize(): int
				{
					return $this->FieldIntValue('thumb_size');
				}

			public function FileExtension(): string
				{
					return pathinfo($this->UploadedFileName(), PATHINFO_EXTENSION);
				}

			public function FilePath(): string
				{
					$filename=$this->TypeTag().'-'.$this->ObjectID().'-'.$this->ID();
					return System::StoragePathForLocalStoredFiles().$filename;
				}

			public function WWWAttachmentMaskedURL(): string
				{
					return '';
				}

			public function ThumbFilePath(): string
				{
					return System::StoragePathForLocalStoredFilesThumbs().'flthmb-'.md5('file'.$this->ID()).'.jpg';
				}

			public function ThumbFileExists(): string
				{
					return file_exists($this->ThumbFilePath());
				}

			public function HasThumb(): bool
				{
					if ($this->FieldIntValue('has_thumb')===1)
						return true;
					elseif ($this->HasCustomThumbURL())
						$has_thumb=true;
					else
						$has_thumb=$this->ThumbFileExists();
					if ($has_thumb && $this->FieldIntValue('has_thumb')===1)
						$this->SetHasThumb(true);
					return $has_thumb;
				}

			public function CreateThumb(): bool
				{
					if ($this->FileExists() && $this->isStoredInLocalFilesystem())
						{
							if ($this->ThumbFileExists())
								unlink($this->ThumbFilePath());
							$width=SystemSettings::ThumbnailWidthDefault();
							$height=SystemSettings::ThumbnailHeightDefault();
							ImageTool::QuickResize($this->FilePath(), $this->ThumbFilePath(), $width, $height, false, 100, true);
							if ($this->ThumbFileExists())
								$this->SetHasThumb(true);
							else
								$this->SetHasThumb(false);
							$this->SaveThumbFileSize();
							return $this->ThumbFileExists();
						}
					return false;
				}

			function Download()
				{
					if ($this->FileExists())
						{
							DownloadHelper::DownloadAsOctetStream(
								$this->FilePath(),
								$this->UploadedFileName()
							);
						}
				}

			function ShowAsJPEG()
				{
					if ($this->FileExists())
						{
							DownloadHelper::DownloadAsJPG(
								$this->FilePath(),
								$this->UploadedFileName()
							);
						}
				}

			function AddFileByCopying($uploaded_file_path=NULL)
				{
					if (!file_exists($uploaded_file_path))
						return false;
					if (file_exists($this->FilePath()))
						unlink($this->FilePath());
					copy($uploaded_file_path, $this->FilePath());
					$this->UpdateValues(Array(
						'uploadedfilename'=>basename($uploaded_file_path),
						'uploadedtime'=>time()
					));
					$this->SaveFileSize();
					return true;
				}

			public function StorageLocationTag(): string
				{
					return $this->FieldStringValue('located');
				}

			protected function SetStorageLocationTag($val)
				{
					$this->UpdateValues(Array('located'=>$val));
				}

			protected function isStoredInLocalFilesystem(): bool
				{
					return $this->StorageLocationTag()===StorageLocation::HOST;
				}

			public function CustomURL(): string
				{
					return $this->FieldStringValue('custom_url');
				}

			protected function SetCustomURL(string $val): void
				{
					$this->UpdateValues(Array('custom_url'=>$val));
				}

			public function HasCustomURL(): bool
				{
					return !empty($this->CustomURL());
				}

			public function CustomThumbURL(): string
				{
					return $this->FieldStringValue('custom_thumb_url');
				}

			protected function SetCustomThumbURL(string $val): void
				{
					$this->UpdateValues(Array('custom_thumb_url'=>$val));
				}

			protected function HasCustomThumbURL(): bool
				{
					return !empty($this->CustomThumbURL());
				}

			protected function AfterRemove(): void
				{
				}

			protected function SetThumbFileSize(int $file_size): void
				{
					$this->UpdateValues(['thumb_size'=>intval($file_size)]);
				}

			protected function SetHasThumb(bool $has_thumb): void
				{
					$this->UpdateValues(['has_thumb'=>intval($has_thumb)]);
				}

		}
