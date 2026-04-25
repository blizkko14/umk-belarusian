/**
 * api.js - Модуль для работы с серверной частью
 */

class API {
    // Базовый URL к папке api
    static baseUrl = 'http://localhost/UMK/api';

    static async request(endpoint, method = 'GET', data = null) {
        const url = `${this.baseUrl}/${endpoint}`;
        
        const options = {
            method: method,
            headers: { 'Content-Type': 'application/json' }
        };

        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            return { success: false, message: 'Ошибка соединения с сервером' };
        }
    }

    // ==================== АВТОРИЗАЦИЯ ====================
    
    static async login(username, password) {
        const result = await this.request('auth/login.php', 'POST', { username, password });
        if (result.success) {
            localStorage.setItem('currentUser', JSON.stringify(result.data.user));
        }
        return result;
    }

    static logout() {
        localStorage.removeItem('currentUser');
        window.location.href = 'authorization.html';
    }

    static isAuthenticated() {
        return localStorage.getItem('currentUser') !== null;
    }

    static getCurrentUser() {
        const user = localStorage.getItem('currentUser');
        return user ? JSON.parse(user) : null;
    }

    static isTeacher() {
        const user = this.getCurrentUser();
        return user && user.role === 'teacher';
    }

    static isStudent() {
        const user = this.getCurrentUser();
        return user && user.role === 'student';
    }

    // ==================== ТЕМЫ ====================
    
    static async getTopics(classId) {
        return await this.request(`topics/get.php?class_id=${classId}`);
    }

    static async createTopic(name, classId) {
        const user = this.getCurrentUser();
        return await this.request('topics/create.php', 'POST', {
            name: name,
            class_id: classId,
            created_by: user ? user.id : 1
        });
    }

    static async updateTopic(id, name) {
        return await this.request('topics/update.php', 'POST', { id, name });
    }

    static async deleteTopic(id) {
        return await this.request('topics/delete.php', 'POST', { id });
    }

    // ==================== ТЕСТЫ ====================
    
    static async getTests(classId) {
        return await this.request(`tests/get.php?class_id=${classId}`);
    }

    static async getTestQuestions(testId) {
        return await this.request(`tests/getquestions.php?test_id=${testId}`);
    }

    static async createTest(title, classId, timeLimit = 20) {
        const user = this.getCurrentUser();
        return await this.request('tests/create.php', 'POST', {
            title: title,
            class_id: classId,
            time_limit: timeLimit,
            created_by: user ? user.id : 1
        });
    }

    static async updateTest(id, title, timeLimit) {
        return await this.request('tests/update.php', 'POST', { id, title, time_limit: timeLimit });
    }

    static async deleteTest(id) {
        return await this.request('tests/delete.php', 'POST', { id });
    }

    static async saveTestResult(testId, studentName, studentClass, score, maxScore) {
        return await this.request('tests/saveresult.php', 'POST', {
            test_id: testId,
            student_name: studentName,
            class: studentClass,
            score: score,
            max_score: maxScore
        });
    }

    // ==================== КОНСПЕКТЫ ====================
    
   
    static async getConspects(classId) {
    return await this.request(`conspects/get.php?class_id=${classId}`);
}

    static async getConspect(id) {
        return await this.request(`conspects/get.php?id=${id}`);
    }

    static async createConspect(conspectData) {
        const user = this.getCurrentUser();
        conspectData.created_by = user ? user.id : 1;
        return await this.request('conspects/create.php', 'POST', conspectData);
    }

    static async updateConspect(conspectData) {
        return await this.request('conspects/update.php', 'POST', conspectData);
    }

    static async deleteConspect(id) {
        return await this.request('conspects/delete.php', 'POST', { id });
    }

    // ==================== МАТЕРИАЛЫ ЦЭ ====================
    
    static async getCEMaterials() {
        return await this.request('cematerials/get.php');
    }

    static async createCEMaterial(materialData) {
        const user = this.getCurrentUser();
        materialData.created_by = user ? user.id : 1;
        return await this.request('cematerials/create.php', 'POST', materialData);
    }

    static async updateCEMaterial(materialData) {
        return await this.request('cematerials/update.php', 'POST', materialData);
    }

    static async deleteCEMaterial(id) {
        return await this.request('cematerials/delete.php', 'POST', { id });
    }
    // Сохранение вопросов теста
static async saveTestQuestions(testId, questions) {
    return await this.request('tests/save-questions.php', 'POST', {
        test_id: testId,
        questions: questions
    });
}
}
