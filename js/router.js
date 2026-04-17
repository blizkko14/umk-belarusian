// router.js - Кіраванне пераходамі паміж старонкамі

class Router {
    /**
     * Перайсці на папярэднюю старонку
     */
    static goBack() {
        if (document.referrer && document.referrer.includes(window.location.origin)) {
            window.history.back();
        } else {
            window.location.href = 'index.html';
        }
    }

    /**
     * Перайсці на галоўную старонку (index.html)
     */
    static goToIndex() {
        window.location.href = 'index.html';
    }

    /**
     * Перайсці да старонкі аўтарызацыі
     */
    static goToAuthorization() {
        window.location.href = 'authorization.html';
    }

    /**
     * Перайсці да ЦЭ (Цэнтралізаваны экзамен) - ca.html
     */
    static goToCE() {
        window.location.href = 'ca.html';
    }

    /**
     * Перайсці да старонкі класаў
     */
    static goToClasses() {
        window.location.href = 'classes.html';
    }

    /**
     * Перайсці да формы дадавання/рэдагавання ЦЭ
     * @param {string} type - Тып матэрыялу ('test', 'link', 'image')
     * @param {number} id - ID матэрыялу для рэдагавання (неабавязкова)
     */
    static goToFormaCA(type = null, id = null) {
        let url = 'forma_ca.html';
        const params = [];
        
        if (type) params.push(`type=${type}`);
        if (id) params.push(`id=${id}`);
        
        if (params.length > 0) {
            url += '?' + params.join('&');
        }
        
        window.location.href = url;
    }

    /**
     * Перайсці да формы стварэння/рэдагавання тэорыі (канспекта)
     * @param {number} id - ID канспекта для рэдагавання (неабавязкова)
     */
    static goToFormaTeory(id = null) {
        if (id) {
            window.location.href = `forma_teory.html?id=${id}`;
        } else {
            window.location.href = 'forma_teory.html';
        }
    }

    /**
     * Перайсці да формы стварэння/рэдагавання тэста
     * @param {number} id - ID тэста для рэдагавання (неабавязкова)
     */
    static goToFormaTests(id = null) {
        if (id) {
            window.location.href = `forma_tests.html?id=${id}`;
        } else {
            window.location.href = 'forma_tests.html';
        }
    }

    /**
     * Перайсці да старонкі раздзелаў (section.html)
     * @param {number} classNumber - Нумар класа (5-11)
     */
    static goToSection(classNumber = null) {
        if (classNumber) {
            window.location.href = `section.html?class=${classNumber}`;
        } else {
            window.location.href = 'section.html';
        }
    }

    /**
     * Перайсці да старонкі тэорыі (канспекта)
     * @param {number} id - ID канспекта
     */
    static goToTeory(id = null) {
        if (id) {
            window.location.href = `teory.html?id=${id}`;
        } else {
            window.location.href = 'teory.html';
        }
    }

    /**
     * Перайсці да старонкі тэм для тэорыі
     * @param {number} classNumber - Нумар класа
     */
    static goToTopicsTeory(classNumber = null) {
        if (classNumber) {
            window.location.href = `topics_teory.html?class=${classNumber}`;
        } else {
            window.location.href = 'topics_teory.html';
        }
    }

    /**
     * Перайсці да старонкі тэстаў
     * @param {number} classNumber - Нумар класа (неабавязкова)
     */
    static goToTests(classNumber = null) {
        if (classNumber) {
            window.location.href = `tests.html?class=${classNumber}`;
        } else {
            window.location.href = 'tests.html';
        }
    }

    /**
     * Перайсці да старонкі праходжання тэста для вучня
     * @param {number} testId - ID тэста
     */
    static goToTestStudent(testId = null) {
        if (testId) {
            window.location.href = `test_student.html?id=${testId}`;
        } else {
            window.location.href = 'test_student.html';
        }
    }

    /**
     * Перайсці да вынікаў тэста
     * @param {number} resultId - ID выніку
     */
    static goToTestResult(resultId) {
        window.location.href = `test-result.html?id=${resultId}`;
    }

    /**
     * Перайсці да статыстыкі
     */
    static goToStatistics() {
        window.location.href = 'statistics.html';
    }

    /**
     * Перайсці да профіля карыстальніка
     */
    static goToProfile() {
        window.location.href = 'profile.html';
    }

    /**
     * Выйсці з сістэмы
     */
    static logout() {
        localStorage.removeItem('currentUser');
        sessionStorage.clear();
        window.location.href = 'index.html';
    }

    /**
     * Перайсці да рэдагавання матэрыялу
     * @param {string} type - Тып матэрыялу ('test', 'teory', 'ca')
     * @param {number} id - ID матэрыялу
     */
    static goToEdit(type, id) {
        switch(type) {
            case 'test':
                Router.goToFormaTests(id);
                break;
            case 'teory':
                Router.goToFormaTeory(id);
                break;
            case 'ca':
                Router.goToFormaCA(null, id);
                break;
            default:
                console.error('Невядомы тып матэрыялу:', type);
        }
    }
}

// Аўтаматычная прывязка кнопак з data-атрыбутамі
document.addEventListener('DOMContentLoaded', () => {
    // Апрацоўка кнопак "Назад"
    const backButtons = document.querySelectorAll('.back_btn, [data-action="back"]');
    backButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            Router.goBack();
        });
    });

    // Апрацоўка кнопак з data-navigate
    document.querySelectorAll('[data-navigate]').forEach(el => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            const page = el.dataset.navigate;
            const param = el.dataset.param;
            const id = el.dataset.id;

            switch(page) {
                case 'index':
                    Router.goToIndex();
                    break;
                case 'authorization':
                    Router.goToAuthorization();
                    break;
                case 'ce':
                case 'ca':
                    Router.goToCE();
                    break;
                case 'classes':
                    Router.goToClasses();
                    break;
                case 'forma_ca':
                    Router.goToFormaCA(param, id);
                    break;
                case 'forma_teory':
                    Router.goToFormaTeory(id);
                    break;
                case 'forma_tests':
                    Router.goToFormaTests(id);
                    break;
                case 'section':
                    Router.goToSection(param);
                    break;
                case 'teory':
                    Router.goToTeory(id);
                    break;
                case 'topics_teory':
                    Router.goToTopicsTeory(param);
                    break;
                case 'tests':
                    Router.goToTests(param);
                    break;
                case 'test_student':
                    Router.goToTestStudent(id);
                    break;
                case 'logout':
                    Router.logout();
                    break;
                case 'profile':
                    Router.goToProfile();
                    break;
                case 'statistics':
                    Router.goToStatistics();
                    break;
                case 'edit':
                    Router.goToEdit(param, id);
                    break;
                default:
                    console.error('Невядомая старонка:', page);
            }
        });
    });
});