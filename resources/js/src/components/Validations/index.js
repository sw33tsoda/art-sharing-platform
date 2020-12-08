import * as Yup from 'yup';

const FILE_SIZE = 160 * 1024;
const SUPPORTED_FORMATS = [
    "image/jpg",
    "image/jpeg",
    "image/gif",
    "image/png"
];

export const SingleArtValidation = Yup.object().shape({
    title:  Yup.string().min(2,'Tối thiểu hai ký tự').max(100,'Tối đa 100 ký tự').required('Không được bỏ trống'),
    caption:   Yup.string().min(2,'Tối thiểu hai ký tự').max(300,'Tối đa 300 ký tự'),
    description:   Yup.string().min(2,'Tối thiểu hai ký tự').max(1500,'Tối đa 1500 ký tự'),
    dimensional:    Yup.number().required('Không được bỏ trống'),
    privacy:   Yup.number().required('Không được bỏ trống'),
    channel:  Yup.number().required('Không được bỏ trống'),
    art : Yup.mixed().required("Không có File").test(
        "fileSize",
        "Kích thước quá lớn, tối đa 2MB",
        value => value && value.size <= FILE_SIZE
    ).test(
        "fileFormat",
        "Định dạng cho phép : JPG, JPEG, PNG",
        value => value && SUPPORTED_FORMATS.includes(value.type)
    ),
    tags: Yup.string(),
}); 